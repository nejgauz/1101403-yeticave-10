<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $value): ?>
            <li class="promo__item promo__item--<?php echo $value['symb_code'] ?? ''; ?>">
                <a class="promo__link"
                   href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
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
