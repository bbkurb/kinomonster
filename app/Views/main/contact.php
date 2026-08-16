<?php
/**
 * Форма обратной связи.
 *
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 */
?>
          <h2>Контакты</h2>
          <hr>

          <?php if (isset($validation) && $validation->getErrors() !== []) : ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($validation->getErrors() as $error) : ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach ?>
              </ul>
            </div>
          <?php endif ?>

          <form role="form" action="<?= base_url('contact') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
              <label for="name">Ваше имя</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name') ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="email">Ваш email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email') ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="subject">Тема</label>
              <input type="text" class="form-control" id="subject" name="subject" value="<?= esc(old('subject') ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="message">Ваш отзыв</label>
              <textarea class="form-control" id="message" name="message" rows="6"><?= esc(old('message') ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-warning pull-right">отправить</button>
            <div class="clearfix"></div>
          </form>
