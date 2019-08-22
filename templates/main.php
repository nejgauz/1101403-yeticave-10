<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $value): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=strip_tags($value['name']);?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($cards as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$item['url'];?>" width="350" height="260" alt="<?=strip_tags($item['name']);?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=strip_tags($item['category']);?></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=strip_tags($item['name']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost">
                                    <?php
                                    $price = priceFormat($item['price']);
                                    echo $price;
                                    ?>
                                </span>
                        </div>
                        <?php
                        $time = timeCounter($item['time']);
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
