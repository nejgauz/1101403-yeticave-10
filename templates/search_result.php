<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item<?php if ($value['name'] === $request): echo " nav__item--current"; endif; ?>">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <? endforeach; ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2><?php echo $header; ?> «<span><?php echo htmlspecialchars($request); ?></span>»</h2>
        <ul class="lots__list">
            <?php echo $items; ?>
        </ul>
    </section>
    <?php if ($pagesNumber > 1): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a href="<?php if ($curPage > 1): $prevPage = $curPage - 1;
                    echo $link . "&page=" . $prevPage; endif; ?>">Назад</a></li>
            <?php for ($i = 1; $i <= $pagesNumber; $i++): ?>
                <li class="pagination-item <?php if ($i == $curPage): echo "pagination-item-active"; endif; ?>"><a
                            href="<?php echo $link . "&page=" . $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="pagination-item pagination-item-next"><a
                        href="<?php if ($curPage < $pagesNumber): $nextPage = $curPage + 1;
                            echo $link . "&page=" . $nextPage; endif; ?>">Вперед</a></li>
        </ul>
    <?php endif; ?>
</div>

