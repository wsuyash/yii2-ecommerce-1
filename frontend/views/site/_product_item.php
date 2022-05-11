<?php
/** @var $model \common\models\Product */
?>

<div class="card h-100">
	<!-- Product image-->
	<a href="#">
		<img class="card-img-top" src="<?php echo $model->getImageUrl(); ?>" alt="..."/>
	</a>
	<!-- Product details-->
	<div class="card-body">
		<h4 class="card-title">
			<a href="#"><?php echo $model->name; ?></a>
		</h4>
		<h5><?php echo Yii::$app->formatter->asCurrency($model->price); ?></h5>
		<div class="card-text">
			<?php echo $model->getShortDescription(); ?>
		</div>
	</div>
	<!-- Product actions-->
	<div class="card-footer text-right">
		<a href="#" class="btn btn-primary">
			Add to Cart
		</a>
	</div>
</div>
