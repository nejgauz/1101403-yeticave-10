<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bids as $bid): ; ?>
            <?php $class = bidClass($bid, $_SESSION['id']); ?>
            <tr class="rates__item <?php echo isset($class['item']) ? $class['item'] : ''; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?php echo $bid['image'] ?? ''; ?>" width="54" height="40" alt="<?php echo htmlspecialchars($bid['lot_title']) ?? ''; ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a
                                    href="lot.php?lot_id=<?php echo $bid['lot_id'] ?? ''; ?>"><?php echo htmlspecialchars($bid['lot_title']) ?? ''; ?></a></h3>
                        <?php if ($class['timer'] === 'timer--win' && isset($bid['contact'])): echo "<p>" . htmlspecialchars($bid['contact']) . "<p>"; endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?php echo $bid['category'] ?? ''; ?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?php echo $class['timer'] ?? ''; ?>"><?php if (isset($class['text'])): echo $class['text']; else: $time = timeCounter($bid['dt_end']);
                            echo $time['days'] . ':' . $time['hours'] . ':' . $time['mins']; endif; ?></div>
                </td>
                <td class="rates__price">
                    <?php echo priceFormat($bid['price']); ?>
                </td>
                <td class="rates__time">
                    <?php echo bidTime($bid['dt_create']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
