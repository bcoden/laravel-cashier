<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-5">
                    <form action="{{ route('subscribe.post') }}" method="post" id="payment-form"
                    data-secret="{{ $intent->client_secret }}">
                        @csrf
                        <div class="mt-4">
                            @foreach(config('store.subscriptions') as $id => $subscription)
                                <input type="radio" name="plan" id="{{ $id }}" value="{{ $subscription['stripe_product_id'] }}" checked>
                                <label for="standard">{{ $subscription['name'] }} ${{ $subscription['costperiunit'] }}/M</label><br/>
                            @endforeach

                        </div>
                        <div class="w-1/2   form-row">
                            <label for="cardHolder-name">Card Holders Name</label>
                            <div>
                                <input type="text" id="cardholder-name" name="cardholder-name" class="px-2 border">
                            </div>
                            <label for="card-element">
                                Credit or debit card
                            </label>
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert"></div>
                        </div>

                        <x-jet-button class="mt-4">
                            Subscribe Now
                        </x-jet-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Create a Stripe client.
            var stripe = Stripe('pk_test_WKttvIWNzWKf9RoXh0iptFAC009PiVXVG9');

            // Create an instance of Elements.
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');
            // Handle real-time validation errors from the card Element.
            card.on('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission.
            const form = document.getElementById('payment-form');
            const cardHolderName = document.getElementById('cardholder-name');
            const clientSecret = form.dataset.secret;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );

                if (error) {
                    var errorElement = document.getElementById('card-errors');
                   errorElement.textContent = error.message;
                } else {
                    stripeTokenHandler(setupIntent);
                }
            });

            // form.addEventListener('submit', function(event) {
            //     event.preventDefault();
            //
            //     stripe.createToken(card).then(function(result) {
            //         if (result.error) {
            //             // Inform the user if there was an error.
            //             var errorElement = document.getElementById('card-errors');
            //             errorElement.textContent = result.error.message;
            //         } else {
            //             // Send the token to your server.
            //             stripeTokenHandler(result.token);
            //         }
            //     });
            // });

            // Submit the form with the token ID.
            function stripeTokenHandler(intent) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', intent.payment_method);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        </script>
    @endpush
</x-app-layout>
