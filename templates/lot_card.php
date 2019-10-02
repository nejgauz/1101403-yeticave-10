<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo strip_tags($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?php echo strip_tags($card['name']) ?? ''; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?php echo $card['url'] ?? ''; ?>" width="730" height="548" alt="<?php echo strip_tags($card['name']) ?? ''; ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?php echo strip_tags($card['category']) ?? ''; ?></span></p>
            <p class="lot-item__description"><?php echo strip_tags($card['description']) ?? ''; ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $time = timeCounter($card['time']);
                $timeStr = $time['hours'] . ':' . $time['mins']; ?>
                <div class="lot-item__timer timer <?php echo timeClass($time); ?>">
                    <?php echo $timeStr; ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost">
                            <?php echo priceFormat($maxPrice); ?>
                        </span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span>
                            <?php echo priceFormat($minBid); ?>
                        </span>
                    </div>
                </div>
                <?php  if (isset($_SESSION['name']) && (strtotime($card['time']) > strtotime('now')) && ($card['user_id'] !== $_SESSION['id']) && (getLastBid($con, $card['id']) !== $_SESSION['id'])): ?>
                    <form class="lot-item__form" action="<?php echo 'lot.php?lot_id=' . $_GET['lot_id']; ?>"
                          method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item <?php echo errorClass($errors, 'cost'); ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost"
                                   placeholder="<?php echo substr_replace(priceFormat($minBid), '', -3); ?>"
                                   value="<?php echo strip_tags(getPostVal('cost')); ?>">
                            <span class="form__error"><?php echo $errors['cost'] ?? ''; ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок (<span><?php echo count($bids); ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bids as $bid): ?>
                        <tr class="history__item">
                            <td class="history__name"><?php echo strip_tags($bid['user_name']) ?? ''; ?></td>
                            <td class="history__price"><?php echo priceFormat($bid['price']) ?? ''; ?></td>
                            <td class="history__time"><?php echo bidTime($bid['dt_create']) ?? ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
