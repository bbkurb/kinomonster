<?php
/**
 * Единый блок вывода ошибок формы.
 *
 * Принимает либо объект валидатора, либо массив ошибок модели, либо строку.
 *
 * @var \CodeIgniter\Validation\ValidationInterface|array<string, string>|null $validation
 * @var string|null                                                           $error
 */

$messages = [];

if (isset($validation)) {
    if (is_array($validation)) {
        $messages = $validation;
    } elseif (is_object($validation) && method_exists($validation, 'getErrors')) {
        $messages = $validation->getErrors();
    }
}

if (! empty($error)) {
    $messages[] = $error;
}
?>

<?php if ($messages !== []) : ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($messages as $message) : ?>
        <li><?= esc($message) ?></li>
      <?php endforeach ?>
    </ul>
  </div>
<?php endif ?>
