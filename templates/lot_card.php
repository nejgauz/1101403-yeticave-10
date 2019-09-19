<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?= $value['name']; ?>"><?= strip_tags($value['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?= $card['name']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= $card['url']; ?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?= $card['category']; ?></span></p>
            <p class="lot-item__description"><?= $card['description']; ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $time = timeCounter($card['time']);
                $timeStr = $time['hours'] . ':' . $time['mins']; ?>
                <div class="lot-item__timer timer <?= timeClass($time); ?>">
                    <?= $timeStr; ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost">
                            <?= priceFormat($maxPrice); ?>
                        </span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span>
                            <?= priceFormat($minBid); ?>
                        </span>
                    </div>
                </div>
                <?php if (isset($_SESSION['name'])): ?>
                    <form class="lot-item__form" action="<?php echo 'lot.php?lot_id=' . $_GET['lot_id']; ?>"
                          method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item <?= errorClass($errors, 'cost'); ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost"
                                   placeholder="<?= substr_replace(priceFormat($minBid), '', -3); ?>"
                                   value="<?= getPostVal('cost'); ?>">
                            <span class="form__error"><?= $errors['cost'] ?? ''; ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php if (isset($_SESSION['name'])): ?>
                <div class="history">
                    <h3>История ставок (<span><?= count($bids); ?></span>)</h3>
                    <table class="history__list">
                        <?php foreach ($bids as $bid): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= $bid['user_name']; ?></td>
                                <td class="history__price"><?= priceFormat($bid['price']); ?></td>
                                <td class="history__time"><?= bidTime($bid['dt_create']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
