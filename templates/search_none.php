<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item<?php if (isset($request) && $value['name'] === $request): echo " nav__item--current"; endif; ?>">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="promo">
    <h2 align="center"><?php echo $text; ?></h2>
</div>


