<?php
/** @var array $items */
?>

<div class="card">
	<h3 class="card-header">
		Your Cart Items
	</h3>
	<div class="card-body p-0">
		<table class="table table-hover">
			<thead>
			<tr>
				<th>Product</th>
				<th>Image</th>
				<th>Unit Price</th>
				<th>Quantity</th>
				<th>Total Price</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($items as $item): ?>
				<tr>
					<td><?php echo $item['name']; ?></td>
					<td>
						<img src="<?php echo \common\models\Product::formatImageUrl($item['image']); ?>"
								 style="width: 50px"
								 alt="<?php echo $item['name']; ?>"
						>
					</td>
					<td><?php echo $item['price']; ?></td>
					<td><?php echo $item['quantity']; ?></td>
					<td><?php echo $item['total_price']; ?></td>
					<td>
						<?php echo \yii\helpers\Html::a('Delete', ['/cart/delete', 'id' => $item['id']], [
							'class' => 'btn btn-outline-danger btn-sm',
							'data-method' => 'post',
							'data-confirm' => 'Are you sure you want to remove this product from the cart?'
						]); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<div class="card-body text-right">
			<a href="<?php echo \yii\helpers\Url::to(['/cart/checkout']); ?>" class="btn btn-primary">Checkout</a>
		</div>
	</div>
</div>
