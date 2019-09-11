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
                $timeStr = $time['days'] . ':' . $time['hours'] . ':' . $time['mins'];
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