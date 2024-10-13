<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderService {
	private static $_instance = null;

  // required
	private $model_id = null;
	private $model_type = null;
	private $type = null;
	private $user_id = null;
	private $order_date = null;

  // optional
	private $fees = [];
	private $notes = null;
	private $discount = 0;
	private $items = [];
  private $sub_total = 0;
  private $grand_total = 0;

  // other
  private $currency = null;
  private $amount = 0;


	public function __construct()
	{

	}

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
  //////////////////////////////////////
  // getters
  //////////////////////////////////////
	public function getModelId() {
		return $this->model_id;
	}
	public function getModelType() {
		return $this->model_type;
	}

	public function getType() {
		return $this->type;
	}
	public function getUserId() {
		return $this->user_id;
	}
	public function getOrderDate() {
		return $this->order_date;
	}

	public function getFees() {
		return $this->fees;
	}
	public function getNotes() {
		return $this->notes;
	}
	public function getItems() {
		return $this->items;
	}
	public function getDiscount() {
		return $this->discount;
	}
	public function getSubTotal() {
		return $this->sub_total;
	}
	public function getGrandTotal() {
		return $this->grand_total;
	}
	public function getCurrency() {
		return $this->currency;
	}
	public function getAmount() {
		return $this->amount;
	}

  //////////////////////////////////////
  // setters
  //////////////////////////////////////
  public function setModelId($model_id) {
		$this->model_id = $model_id;
		return $this;
	}
  public function setModelType($model_type) {
		$this->model_type = $model_type;
		return $this;
	}

	public function setType($type) {
		$this->type = $type;
		return $this;
	}
	public function setUserId($user_id) {
		$this->user_id = $user_id;
		return $this;
	}
	public function setOrderDate($order_date) {
		$this->order_date = $order_date;
		return $this;
	}

	public function setFees($fees) {
		$this->fees = $fees;
		return $this;
	}
	public function setNotes($notes) {
		$this->notes = $notes;
		return $this;
	}
	public function setItems($items) {
		$this->items = $items;
		return $this;
	}
	public function setDiscount($discount) {
		$this->discount = $discount;
		return $this;
	}
	public function setSubtotal($sub_total) {
		$this->sub_total = $sub_total;
		return $this;
	}
	public function setGrandtotal($grand_total) {
		$this->grand_total = $grand_total;
		return $this;
	}
  public function setCurrency($currency) {
    $this->currency = $currency;
		return $this;
	}
	public function setAmount($amount) {
    $this->amount = $amount;
		return $this;
	}

  //////////////////////////////////////
  // other methods
  //////////////////////////////////////
  public function createOrder() {
    $model_id     = $this->getModelId();
    $model_type   = $this->getModelType();
    $type         = $this->getType();
    $user_id      = $this->getUserId();
    $order_date   = $this->getOrderDate();

    $fees         = $this->getFees() ?? [];
    $discount     = $this->getDiscount();
    $sub_total    = $this->getSubTotal();
    $grand_total  = $this->getGrandTotal();
    $notes        = $this->getNotes();

    $order = Order::create([
      'orderable_id'    => $model_id,
      'orderable_type'  => $model_type,
      'type'            => $type,
      'user_id'         => $user_id,
      'fees'            => json_encode($fees),
      'discount'        => $discount,

      'sub_total'       => $sub_total,
      'grand_total'     => $grand_total,
      'total_remaining' => $grand_total,
      'notes'           => $notes,
    ]);

    $order->save();

    if($order) {
      $user = User::find($user_id);

      if($user->balance > 0) {
        $deducted = min($user->balance, $order->total_remaining);

        $order->total_remaining -= $deducted;
        $order->total_paid += $deducted;
      }

      $user->recalculateBalance();
      $order->save();
    }

    return $order;

  }

  public function createUserPayment() {
    $user_id      = $this->getUserId();
    $currency     = $this->getCurrency();
    $amount       = $this->getAmount();
    $notes        = $this->getNotes();

    $payment = Payment::create([
      'user_id' => $user_id,
      'amount'  => $amount,
      'currency'=> $currency,
      'notes'   => $notes,
    ]);

    $user   = User::find($user_id);
    $orders = Order::where('user_id', $user_id)
      ->whereIn('status', [Order::STATUS_UNPAID, Order::STATUS_PARTIAL_PAID])
      ->orderBy('created_at')
      ->get();

    if($payment) {
      foreach ($orders as $key => $order) {
        $deducted = min($amount, $order->total_remaining);

        $order->total_remaining -= $deducted;
        $order->total_paid += $deducted;
        $amount -= $deducted;

        $order->save();
        if($amount == 0) {
          break;
        }
      }
      $user->recalculateBalance();
    }

    return $payment;
  }
}
