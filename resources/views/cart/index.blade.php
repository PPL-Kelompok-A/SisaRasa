@extends('layouts.navbar')
@section('content')
    <style>
    .container {
      display: flex;
      flex-direction: column;
      align-items: center; 
      padding: 2rem;
      max-width: 800px;
      margin: 0 auto; 
    }

    .card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      background-color: #fff;
      width: 100%; 
    }

    .card img {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
    }

    .card-content {
      display: flex;
      align-items: center;
      flex: 1;
    }

    .details {
      margin-left: 1rem;
    }

    .details h4 {
      margin: 0;
      color: #045c4b;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-right: 1rem;
    }

    .quantity-btn {
      cursor: pointer;
      background: #ddd;
      border: none;
      padding: 0.2rem 0.6rem;
      border-radius: 4px;
    }

    .trash {
      cursor: pointer;
      margin-left: 1rem;
      color: red;
      font-size: 1.2rem;
    }

    .checkout {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }

    .checkout button {
      background-color: #045c4b; 
      color: white;
      border: none;
      padding: 0.8rem 2rem;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
    }

    .checkout button[disabled] {
      background-color: #ccc;
      cursor: not-allowed;
    }

    .total {
      text-align: right;
      font-weight: bold;
      margin-top: 1rem;
      font-size: 1.2rem;
      width: 100%; 
    }
    </style>

  <div class="container" id="cart-container">
    @foreach ($cartItems as $item)
    <div class="card">
      <div class="card-content">
        <img src="{{ $item->img }}" alt="{{ $item->name }}" />
        <div class="details">
          <h4>{{ $item->name }}</h4>
          <p>{{ $item->desc }}</p>
          <p><strong>{{ number_format($item->price, 0, ',', '.') }}</strong></p>
        </div>
      </div>
      <div class="quantity-control">
        <form action="/cart/{{ $item->id }}/quantity" method="POST">
          @csrf
          <button class="quantity-btn" type="submit" name="delta" value="-1">-</button>
        </form>
        <span>{{ $item->quantity }}</span>
        <form action="/cart/{{ $item->id }}/quantity" method="POST">
          @csrf
          <button class="quantity-btn" type="submit" name="delta" value="1">+</button>
        </form>
      </div>
      <div>
        <form action="/cart/{{ $item->id }}/select" method="POST">
          @csrf
          <input type="checkbox" {{ $item->selected ? 'checked' : '' }} onchange="this.form.submit()" />
        </form>
      </div>
      <div class="trash">
        <form action="/cart/{{ $item->id }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="trash-btn">🗑️</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>

  <div class="container total" id="total-price">
    Total: {{ number_format($cartItems->where('selected', true)->sum(function($item) {
      return $item->price * $item->quantity;
  }), 0, ',', '.') }}
    </div>

  <div class="checkout">
    <form action="{{ route('cart.checkout') }}" method="POST">
      @csrf
      <button type="submit" id="checkoutBtn" {{ $cartItems->where('selected', true)->isEmpty() ? 'disabled' : '' }}>Checkout</button>
    </form>
  </div>

  @if(session('success'))
    <div>{{ session('success') }}</div>
  @elseif(session('error'))
    <div>{{ session('error') }}</div>
  @endif
@endsection