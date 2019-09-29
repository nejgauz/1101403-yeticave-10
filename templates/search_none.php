<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item<?php if (isset($request) && $value['name'] === $request): echo " nav__item--current"; endif; ?>">
                <a href="search_category.php?category=<?= $value['id']; ?>"><?= strip_tags($value['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="promo">
    <h2 align="center"><?= $text; ?></h2>
</div>


