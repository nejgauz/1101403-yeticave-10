<nav class="nav">
    <ul class="nav__list container">
        <?php foreach($categories as $value): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=strip_tags($value['name']);?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?=$card[0]['name'];?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?=$card[0]['url'];?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?=$card[0]['category'];?></span></p>
            <p class="lot-item__description"><?=$card[0]['description'];?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $timeClass = 'lot-item__timer timer';
                $time = timeCounter($card[0]['time']);
                $timeStr = $time['days'] . ':' . $time['hours'] . ':' . $time['mins'];
                if ($time['hours'] < 1 && $time['days'] == 0) {
                    $timeClass .= ' timer--finishing';
                } ?>
                <div class="<?=$timeClass;?>">
                    <?=$timeStr;?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost">
                            <?php
                            $maxBid = getMaxBid($connection , $card[0]['id']);
                            $maxBid = $maxBid['max_price'];
                            $curPrice = $card[0]['st_price'];
                            if ($curPrice > $maxBid) {
                                $maxPrice = $curPrice;
                                echo priceFormat($curPrice);
                            } else {
                                $maxPrice = $maxBid;
                                echo priceFormat($maxBid);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span>
                            <?php $minBid = $maxPrice + $card[0]['step']; echo priceFormat($minBid); ?>
                        </span>
                    </div>
                </div>
                <?php if (isset($_SESSION['name'])): ?>
                    <form class="lot-item__form" action="lot.php" method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item form__item--invalid">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost" placeholder="12 000">
                            <span class="form__error">Введите наименование лота</span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
