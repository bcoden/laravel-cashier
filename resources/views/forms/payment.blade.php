<form action="{{ $action }}" method="post" id="payment-form"
      @if(isset($intent))
        data-secret="{{ $intent->client_secret }}"
      @endif
>
    @csrf
    <div class="mt-4 form-row">
        @if(isset($product))
            <h2 class="font-bold">{{ $product['title'] }}</h2>
            <p>{{ $product['description'] }}</p>
            <i>${{ number_format($product['price'],2) }}</i>
        @elseif(isset($subscription))
            @foreach(config('store.subscriptions') as $id => $subscription)
                <input type="radio" name="plan" id="{{ $id }}" value="{{ $subscription['stripe_product_id'] }}" checked>
                <label for="standard">{{ $subscription['name'] }} ${{ $subscription['costperiunit'] }}/M</label><br/>
            @endforeach
        @endif
    </div>

    <div class="form-row my-4">
        <label for="cardHolder-name" class="font-bold">Card Holders Name</label>
        <div>
            <input type="text" id="cardholder-name" name="cardholder-name" class="p-2 rounded border w-full">
        </div>
    </div>

    <div class="form-row my-4">
        <label for="card-element" class="font-bold">
            Credit or debit card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>

        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>
    </div>

    <x-jet-button class="mt-4">
        {{ $action_text }}
    </x-jet-button>
</form>