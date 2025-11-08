@extends('layouts.sneat')

@section('title', 'Payment Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / Payment /</span> {{ $loan->loan_id }}
    </h4>
    <div class="d-flex gap-2">
        @if($loan->status !== 'completed')
        <form action="{{ route('admin.loan.complete', $loan->loan_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this loan as completed? This action cannot be undone.');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="bx bx-check"></i> Mark as Completed
            </button>
        </form>
        @endif
        <a href="{{ route('admin.payment.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to Payments
        </a>
    </div>
</div>

<!-- Loan Calculation Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-primary">Loan Information</h6>
                <hr>
                <div class="mb-3">
                    <small class="text-muted">Loan Amount</small>
                    <h4 class="mb-0 text-success">₹{{ number_format($loan->loan_amount, 2) }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Processing Fee (5%)</small>
                    <h5 class="mb-0">₹{{ number_format($loan->processing_fee, 2) }}</h5>
                </div>
                <div class="mb-3">
                    <small class="text-muted">In-Hand Amount</small>
                    <h5 class="mb-0 text-primary">₹{{ number_format($loan->in_hand_amount, 2) }}</h5>
                </div>
                <div class="mb-0">
                    <small class="text-muted">Tenure</small>
                    <h5 class="mb-0"><span class="badge bg-label-info">{{ $loan->tenure }} months</span></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-success">Payment Calculation</h6>
                <hr>
                <div class="mb-3">
                    <small class="text-muted">Interest Rate</small>
                    <h5 class="mb-0">15% per 3 months</h5>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Total Amount (with interest)</small>
                    <h4 class="mb-0 text-success">₹{{ number_format($loan->total_amount_with_interest, 2) }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Daily EMI</small>
                    <h5 class="mb-0 text-primary">₹{{ number_format($loan->daily_emi, 2) }}</h5>
                </div>
                <div class="mb-0">
                    <small class="text-muted">Late Fee (per day after 3 days)</small>
                    <h5 class="mb-0 text-danger">₹{{ number_format($loan->late_fee_per_day, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-warning">Payment Statistics</h6>
                <hr>
                <div class="mb-3">
                    <small class="text-muted">Total Transactions</small>
                    <h5 class="mb-0">{{ $loan->total_transactions }}</h5>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Completed</small>
                        <span class="badge bg-success">{{ $loan->completed_transactions }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Pending</small>
                        <span class="badge bg-warning">{{ $loan->pending_transactions }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Delayed</small>
                        <span class="badge bg-danger">{{ $loan->delayed_transactions }}</span>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <small class="text-muted">Total Paid</small>
                    <h5 class="mb-0 text-success">₹{{ number_format($loan->total_paid, 2) }}</h5>
                </div>
                <div class="mb-0">
                    <small class="text-muted">Remaining Amount</small>
                    <h5 class="mb-0 text-danger">₹{{ number_format($loan->remaining_amount, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Entry Form -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bx bx-money me-2"></i>Record Daily Payment</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.payment.record', $loan->loan_id) }}" method="POST" id="paymentForm">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label for="transaction_id" class="form-label">Select Payment Date *</label>
                    <select class="form-select" id="transaction_id" name="transaction_id" required>
                        <option value="">Select a date...</option>
                        @foreach($loan->transactions->where('status', '!=', 'completed') as $idx => $transaction)
                            <option value="{{ $transaction->id }}" 
                                    data-emi="{{ $transaction->amount }}" 
                                    data-late-fee="{{ $transaction->late_fee }}">
                                {{ $transaction->due_date->format('M d, Y') }}
                                @if($transaction->status === 'delayed')
                                    (Delayed - Late Fee: ₹{{ number_format($transaction->late_fee, 2) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('transaction_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="paid_amount" class="form-label">Amount Paid (₹) *</label>
                    <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" 
                           placeholder="Enter amount" required>
                    @error('paid_amount')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                           value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-check"></i> Record Payment
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" 
                              placeholder="Enter any additional notes..."></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Calculation Example -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">💡 Calculation Example</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3">Loan Breakdown:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Loan Amount:</strong> ₹{{ number_format($loan->loan_amount, 2) }}</li>
                    <li class="mb-2"><strong>- Processing Fee (5%):</strong> ₹{{ number_format($loan->processing_fee, 2) }}</li>
                    <li class="mb-2"><strong>= In-Hand Amount:</strong> ₹{{ number_format($loan->in_hand_amount, 2) }}</li>
                    <li class="mb-2"><strong>+ Interest (15%/3m):</strong> ₹{{ number_format($loan->total_amount_with_interest - $loan->loan_amount, 2) }}</li>
                    <li class="mb-0"><strong>= Total to Pay:</strong> ₹{{ number_format($loan->total_amount_with_interest, 2) }}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3">Payment Schedule:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Daily EMI:</strong> ₹{{ number_format($loan->daily_emi, 2) }} (for {{ $loan->tenure * 30 }} days)</li>
                    <li class="mb-2"><strong>Late Fee:</strong> No fee for first 3 missed days</li>
                    <li class="mb-2"><strong>After 3 days:</strong> ₹{{ number_format($loan->late_fee_per_day, 2) }} per day</li>
                    <li class="mb-0"><strong>Example:</strong> 
                        <br>Day 1-3: ₹{{ number_format($loan->daily_emi, 2) }}/day
                        <br>Day 4+: ₹{{ number_format($loan->daily_emi + $loan->late_fee_per_day, 2) }}/day (with late fee)
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details -->
<div class="card">
    <h5 class="card-header">Daily Payment Transactions</h5>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Due Date</th>
                        <th>EMI Amount</th>
                        <th>Paid Amount</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th>Days Late</th>
                        <th>Late Fee</th>
                        <th>Total Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loan->transactions as $index => $transaction)
                    <tr>
                        <td><strong>Day {{ $index + 1 }}</strong></td>
                        <td>{{ $transaction->due_date->format('M d, Y') }}</td>
                        <td class="text-success">₹{{ number_format($transaction->amount, 2) }}</td>
                        <td class="text-primary">
                            @if($transaction->paid_amount && $transaction->paid_amount > 0)
                                <strong>₹{{ number_format($transaction->paid_amount, 2) }}</strong>
                                @php
                                    $expectedAmount = $transaction->amount + $transaction->late_fee;
                                    $remaining = $expectedAmount - $transaction->paid_amount;
                                @endphp
                                @if($remaining > 0)
                                    <br><small class="text-muted">(Remaining: ₹{{ number_format($remaining, 2) }})</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->status === 'completed')
                                <span class="badge bg-success">Paid</span>
                            @elseif($transaction->status === 'delayed')
                                <span class="badge bg-danger">Delayed</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $transaction->paid_date ? $transaction->paid_date->format('M d, Y') : '-' }}</td>
                        <td>
                            @if($transaction->days_late > 0)
                                <span class="badge bg-danger">{{ $transaction->days_late }} days</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-danger">
                            @if($transaction->late_fee > 0)
                                ₹{{ number_format($transaction->late_fee, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td><strong>₹{{ number_format($transaction->amount + $transaction->late_fee, 2) }}</strong></td>
                        <td>
                            @if($transaction->status !== 'completed')
                                <button class="btn btn-sm btn-success record-payment-btn" 
                                        data-transaction-id="{{ $transaction->id }}"
                                        data-emi="{{ $transaction->amount }}"
                                        data-late-fee="{{ $transaction->late_fee }}"
                                        data-due-date="{{ $transaction->due_date->format('Y-m-d') }}">
                                    <i class="bx bx-money"></i> Pay Now
                                </button>
                            @else
                                <span class="badge bg-success"><i class="bx bx-check"></i> Paid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <p class="mb-0">No transactions found for this loan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-fill payment amount when date is selected
    document.getElementById('transaction_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const emi = parseFloat(selectedOption.getAttribute('data-emi')) || 0;
        const lateFee = parseFloat(selectedOption.getAttribute('data-late-fee')) || 0;
        const totalAmount = emi + lateFee;
        
        document.getElementById('paid_amount').value = totalAmount.toFixed(2);
    });

    // Handle "Pay Now" button clicks
    document.querySelectorAll('.record-payment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            const emi = parseFloat(this.getAttribute('data-emi')) || 0;
            const lateFee = parseFloat(this.getAttribute('data-late-fee')) || 0;
            const totalAmount = emi + lateFee;

            // Fill the form
            document.getElementById('transaction_id').value = transactionId;
            document.getElementById('paid_amount').value = totalAmount.toFixed(2);
            
            // Scroll to the payment form
            document.getElementById('paymentForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight the form
            document.querySelector('.card.border-primary').classList.add('border-success');
            setTimeout(() => {
                document.querySelector('.card.border-primary').classList.remove('border-success');
            }, 2000);
        });
    });
</script>
@endpush

@endsection

