<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="product-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')->widget(CKEditor::class, [
		'options' => ['rows' => 6],
		'preset' => 'basic'
	]) ?>

	<!--	<div class="input-group mb-3">-->
	<!--		<div class="custom-file">-->
	<!--			<input type="file" class="custom-file-input" id="inputGroupFile01">-->
	<!--			<label class="custom-file-label" for="inputGroupFile01">Choose image for product</label>-->
	<!--		</div>-->
	<!--	</div>-->

	<?= $form->field($model, 'image', [
		'template' => '
			<div class="custom-file">
				{input}	
				{label}
				{error}
			</div>
		',
		'labelOptions' => ['class' => 'custom-file-label'],
		'inputOptions' => ['class' => 'custom-file-input']
	])->textInput(['type' => 'file']); ?>

	<?= $form->field($model, 'price')->textInput([
		'maxlength' => true,
		'type' => 'number'
	]) ?>

	<?= $form->field($model, 'status')->checkbox(); ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
