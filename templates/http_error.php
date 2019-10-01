<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?= $value['id'] ?? ''; ?>"><?= strip_tags($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?= $error; ?></h2>
    <p><?= $text; ?></p>
</section>
