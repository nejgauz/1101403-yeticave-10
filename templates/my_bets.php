<nav class="nav">
    <ul class="nav__list container">
        <?php foreach($categories as $value): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?=strip_tags($value['name']);?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bids as $bid): ;?>
            <?php $class = bidClass($bid, $_SESSION['id']);?>
            <tr class="rates__item <?=$class['item'];?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?=$bid['image'];?>" width="54" height="40" alt="<?=$bid['category'];?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a href="lot.php?lot_id=<?=$bid['lot_id'];?>"><?=$bid['lot_title'];?></a></h3>
                        <?php if ($class['timer'] === 'timer--win'): echo "<p>" . $bid['contact'] . "<p>"; endif;?>
                    </div>
                </td>
                <td class="rates__category">
                    <?=$bid['category'];?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?=$class['timer'];?>"><?php if (isset($class['text'])): echo $class['text']; else: $time = timeCounter($bid['dt_end']); echo $time['days'] . ':' . $time['hours'] . ':' . $time['mins']; endif; ?></div><!-- timer--win --> <!-- timer--finishing --> <!-- timer--end -->
                </td>
                <td class="rates__price">
                    <?=priceFormat($bid['price']);?>
                </td>
                <td class="rates__time">
                    <?=bidTime($bid['dt_create']);?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
