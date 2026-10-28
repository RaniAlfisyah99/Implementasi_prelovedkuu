<?php
class PrelovedFileStore {
    private string $file;
    public function __construct() {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $this->file = $dir . '/store.json';
        if (!is_file($this->file)) file_put_contents($this->file, json_encode(['users'=>[], 'products'=>[], 'next_user_id'=>1, 'next_product_id'=>1], JSON_PRETTY_PRINT), LOCK_EX);
    }
    private function read(): array { $d=json_decode((string)file_get_contents($this->file),true); return is_array($d)?$d:['users'=>[],'products'=>[],'next_user_id'=>1,'next_product_id'=>1]; }
    private function write(array $d): void { file_put_contents($this->file, json_encode($d, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX); }
    public function prepare(string $sql): PrelovedFileStatement { return new PrelovedFileStatement($this, $sql); }
    public function query(string $sql): PrelovedFileStatement { $s=$this->prepare($sql); $s->execute([]); return $s; }
    public function run(string $sql,array $p): array {
        $d=$this->read(); $low=strtolower(preg_replace('/\s+/', ' ', trim($sql))); $rows=[];
        if (str_starts_with($low,'insert into users')) { $d['users'][]=['id'=>$d['next_user_id']++,'name'=>$p[0],'email'=>$p[1],'password'=>$p[2]]; $this->write($d); return []; }
        if (str_starts_with($low,'insert into products')) { $d['products'][]=['id'=>$d['next_product_id']++,'user_id'=>(int)$p[0],'title'=>$p[1],'description'=>$p[2],'price'=>(int)$p[3],'category'=>$p[4],'condition_label'=>$p[5],'image'=>$p[6],'status'=>$p[7],'created_at'=>date('Y-m-d H:i:s')]; $this->write($d); return []; }
        if (str_starts_with($low,'update products')) { foreach($d['products'] as &$r) if((int)$r['id']==(int)$p[7] && (int)$r['user_id']==(int)$p[8]) { [$r['title'],$r['description'],$r['price'],$r['category'],$r['condition_label'],$r['image'],$r['status']]=array_slice($p,0,7); } unset($r); $this->write($d); return []; }
        if (str_starts_with($low,'delete from products')) { $before=count($d['products']); $d['products']=array_values(array_filter($d['products'],fn($r)=>(int)$r['id']!==(int)$p[0] || (int)$r['user_id']!==(int)$p[1])); $this->affected=$before-count($d['products']); $this->write($d); return []; }
        if (str_contains($low,'count(*) as total')) { $mine=array_values(array_filter($d['products'],fn($r)=>(int)$r['user_id']==(int)$p[0])); return [['total'=>count($mine),'available'=>count(array_filter($mine,fn($r)=>$r['status']==='available')),'sold'=>count(array_filter($mine,fn($r)=>$r['status']==='sold'))]]; }
        if (str_contains($low,'from users where email')) { foreach($d['users'] as $u) if(strtolower($u['email'])===strtolower((string)$p[0])) { $rows[]=$u; break; } return $rows; }
        if (str_contains($low,'from products')) {
            $users=[]; foreach($d['users'] as $u)$users[$u['id']]=$u;
            foreach($d['products'] as $r) { if(str_contains($low,'where products.id = ?') || preg_match('/where id = \?/', $low)) { if((int)$r['id']!==(int)($p[0]??0))continue; if(str_contains($low,'user_id = ?') && (int)$r['user_id']!==(int)($p[1]??0))continue; }
                elseif(str_contains($low,'where user_id = ?')) { if((int)$r['user_id']!==(int)($p[0]??0))continue; }
                else { if(str_contains($low,"status = 'available'") && $r['status']!=='available')continue; $i=0; if(str_contains($low,'title like ?')) { $term=str_replace('%','', (string)($p[$i++]??'')); $descTerm=$p[$i++]??''; $catTerm=$p[$i++]??''; if($term!=='' && stripos($r['title'].' '.$r['description'].' '.$r['category'],$term)===false)continue; } if(str_contains($low,'products.category = ?')) { if($r['category']!==($p[$i++]??null))continue; } if(str_contains($low,'products.condition_label = ?')) { if($r['condition_label']!==($p[$i++]??null))continue; } }
                $row=$r; if(str_contains($low,'join users')) { $u=$users[$r['user_id']]??['name'=>'Pengguna','email'=>'']; $row['seller_name']=$u['name']; if(str_contains($low,'seller_email'))$row['seller_email']=$u['email']; }
                if(str_contains($low,'select image from'))$row=['image'=>$r['image']];
                $rows[]=$row;
            }
            if(str_contains($low,'order by products.price asc'))usort($rows,fn($a,$b)=>$a['price']<=>$b['price']); elseif(str_contains($low,'order by products.price desc'))usort($rows,fn($a,$b)=>$b['price']<=>$a['price']); elseif(str_contains($low,'order by products.title asc'))usort($rows,fn($a,$b)=>strcmp($a['title'],$b['title'])); else usort($rows,fn($a,$b)=>strcmp($b['created_at']??'', $a['created_at']??''));
            if(preg_match('/limit (\d+)/',$low,$m))$rows=array_slice($rows,0,(int)$m[1]); return $rows;
        }
        return $rows;
    }
    public int $affected=0;
}
class PrelovedFileStatement {
    private PrelovedFileStore $db; private string $sql; private array $rows=[]; private int $pos=0;
    public function __construct($db,$sql){$this->db=$db;$this->sql=$sql;}
    public function execute(array $params=[]): bool { $this->rows=$this->db->run($this->sql,$params); $this->pos=0; return true; }
    public function fetch(){return $this->rows[$this->pos++]??false;}
    public function fetchAll(): array{return $this->rows;}
    public function rowCount(): int{return $this->db->affected;}
}
