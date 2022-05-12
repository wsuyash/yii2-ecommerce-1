<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use Yii;
use common\models\OrderAddress;
use common\models\Product;
use common\models\User;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends \frontend\base\Controller
{
	public function behaviors()
	{
		return [
			[
				'class' => ContentNegotiator::class,
				'only' => ['add'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON
				],
			],
			[
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST', 'DELETE'],
				]
			]
		];
	}

	public function actionIndex()
	{
		if (\Yii::$app->user->isGuest) {
			$cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);
		} else {
			$cartItems = CartItem::getItemsForUser(currUserId());
		}

		return $this->render('index', [
			'items' => $cartItems
		]);
	}

	public function actionAdd()
	{
		$id = \Yii::$app->request->post('id');
		$product = Product::find()->id($id)->published()->one();

		if (!$product) {
			throw new NotFoundHttpException('Product does not exist');
		}

		if (\Yii::$app->user->isGuest) {

			$cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);
			$found = false;

			foreach ($cartItems as &$item) {
				if ($item['id'] == $id) {
					$item['quantity']++;
					$found = true;
					break;
				}
			}

			if (!$found) {
				$cartItem = [
					'id' => $id,
					'name' => $product->name,
					'image' => $product->image,
					'price' => $product->price,
					'quantity' => 1,
					'total_price' => $product->price
				];

				$cartItems[] = $cartItem;
			}

			\Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);

		} else {
			$userId = \Yii::$app->user->id;
			$cartItem = CartItem::find()->userId($userId)->productId($id)->one();

			if ($cartItem) {
				$cartItem->quantity++;
			} else {
				$cartItem = new CartItem();
				$cartItem->product_id = $id;
				$cartItem->created_by = \Yii::$app->user->id;
				$cartItem->quantity = 1;
			}

			if ($cartItem->save()) {
				return [
					'success' => true
				];
			} else {
				return [
					'success' => false,
					'errors' => $cartItem->errors
				];
			}
		}
	}

	public function actionDelete($id)
	{
		if (isGuest()) {
			$cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);

			foreach ($cartItems as $i => $cartItem) {
				if ($cartItem['id'] == $id) {
					array_splice($cartItems, $i, 1);
					break;
				}
			}

			\Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
		} else {
			CartItem::deleteAll(['product_id' => $id, 'created_by' => currUserId()]);
		}

		return $this->redirect(['index']);
	}

	public function actionChangeQuantity()
	{
		$id = \Yii::$app->request->post('id');
		$product = Product::find()->id($id)->published()->one();

		if (!$product) {
			throw new NotFoundHttpException('Product does not exist');
		}

		$quantity = \Yii::$app->request->post('quantity');

		if (isGuest()) {
			$cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);

			foreach ($cartItems as &$cartItem) {
				if ($cartItem['id'] == $id) {
					$cartItem['quantity'] = $quantity;
					break;
				}
			}

			\Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
		} else {
			$cartItem = CartItem::find()->userId(currUserId())->productId($id)->one();

			if ($cartItem) {
				$cartItem->quantity = $quantity;
				$cartItem->save();
			}
		}

		return CartItem::getTotalQuantityForUser(currUserId());
	}

	public function actionCheckout()
	{
		/** @var User $user */
		$order = new Order();
		$orderAddress = new OrderAddress();

		if (!isGuest()) {
			$user = Yii::$app->user->identity;
			$userAddress = $user->getAddress();

			$order->firstname = $user->firstname;
			$order->lastname = $user->lastname;
			$order->email = $user->email;
			$order->status = Order::STATUS_DRAFT;

			$orderAddress->address = $userAddress->address;

			$orderAddress = new OrderAddress();
			$orderAddress->address = $userAddress->address;
			$orderAddress->city = $userAddress->city;
			$orderAddress->state = $userAddress->state;
			$orderAddress->country = $userAddress->country;
			$orderAddress->zipcode = $userAddress->zipcode;

			$cartItems = CartItem::getItemsForUser(currUserId());
		} else {
			$cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
		}

		$productQuantity = CartItem::getTotalQuantityForUser(currUserId());
		$totalPrice = CartItem::getTotalPriceForUser(currUserId());

		return $this->render('checkout', [
			'order' => $order,
			'orderAddress' => $orderAddress,
			'cartItems' => $cartItems,
			'productQuantity' => $productQuantity,
			'totalPrice' => $totalPrice
		]);
	}
}