<article class="product-card">
    <a class="product-image-wrap" href="product.php?id=<?= (int) $product['id'] ?>">
        <img class="product-image" src="<?= e(product_image($product['image'] ?? null)) ?>" alt="<?= e($product['title']) ?>" loading="lazy">
        <span class="product-condition"><?= e($product['condition_label']) ?></span>
        <span class="product-arrow">↗</span>
    </a>
    <div class="product-info">
        <span class="product-category"><?= e($product['category']) ?></span>
        <h3><a href="product.php?id=<?= (int) $product['id'] ?>"><?= e($product['title']) ?></a></h3>
        <div class="product-bottom">
            <strong><?= rupiah($product['price']) ?></strong>
            <span>oleh <?= e($product['seller_name'] ?? 'Penjual') ?></span>
        </div>
    </div>
</article>
