<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $value): ?>
                <li class="nav__item">
                    <a href="all-lots.html"><?=strip_tags($value['name']);?></a>
                </li>
            <? endforeach; ?>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?=strip_tags($request);?></span>»</h2>
            <ul class="lots__list">
                <?php foreach ($cards as $item): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?=$item['image_path'];?>" width="350" height="260" alt="<?=strip_tags($item['title']);?>">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?=strip_tags($item['category_name']);?></span>
                            <h3 class="lot__title"><a class="text-link" href="<?php echo 'lot.php?lot_id=' . $item['id']; ?>"><?=strip_tags($item['title']);?></a></h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost">
                                    <?php
                                    $price = priceFormat($item['st_price']);
                                    echo $price;
                                    ?>
                            </span>
                                </div>
                                <?php
                                $time = timeCounter($item['dt_end']);
                                $timeStr = $time['hours'] . ':' . $time['mins'];
                                if ($time['hours'] < 1 && $time['days'] == 0): ?>
                                    <div class="lot__timer timer timer--finishing">
                                        <?=$timeStr;?>
                                    </div>
                                <?php else: ?>
                                    <div class="lot__timer timer">
                                        <?=$timeStr;?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php if (count($cards) > 9): ?>
            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
                <li class="pagination-item pagination-item-active"><a>1</a></li>
                <li class="pagination-item"><a href="#">2</a></li>
                <li class="pagination-item"><a href="#">3</a></li>
                <li class="pagination-item"><a href="#">4</a></li>
                <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
            </ul>
        <?php endif; ?>
    </div>
</main>
