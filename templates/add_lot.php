<nav class="nav">
    <ul class="nav__list container">
        <?php foreach($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?=$value['name'];?>"><?=strip_tags($value['name']);?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form form--add-lot container <?php if (!empty($errors)): echo 'form--invalid'; endif; ?>" action="add.php" method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?=errorClass($errors, 'lot-name');?>">
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=getPostVal('lot-name');?>">
            <span class="form__error"><?=$errors['lot-name'] ?? '';?></span>
        </div>
        <div class="form__item <?=errorClass($errors, 'category');?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <option>Выберите категорию</option>
                <?php foreach ($categories as $value): ?>
                    <option value="<?=$value['id'];?>" <?php if ($value['id'] == getPostVal('category')): echo 'selected'; endif; ?>><?=strip_tags($value['name']);?></option>
                <?php endforeach; ?>
            </select>
            <span class="form__error"><?=$errors['category'] ?? '';?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?=errorClass($errors, 'message');?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?=getPostVal('message');?></textarea>
        <span class="form__error"><?=$errors['message'] ?? '';?></span>
    </div>
    <div class="form__item form__item--file <?=errorClass($errors, 'image');?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="image" id="lot-img" value="<?=getPostVal('path');?>">
            <label for="lot-img">
                Добавить
            </label>
            <span class="form__error"><?=$errors['image'] ?? '';?></span>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?=errorClass($errors, 'lot-rate');?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?=getPostVal('lot-rate');?>">
            <span class="form__error"><?=$errors['lot-rate'] ?? '';?></span>
        </div>
        <div class="form__item form__item--small <?=errorClass($errors, 'lot-step');?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?=getPostVal('lot-step');?>">
            <span class="form__error"><?=$errors['lot-step'] ?? '';?></span>
        </div>
        <div class="form__item <?=errorClass($errors, 'lot-date');?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?=getPostVal('lot-date');?>">
            <span class="form__error"><?=$errors['lot-date'] ?? '';?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>


