-- ============================================================================
-- TEST DATA SQL QUERIES FOR LATE FEE TESTING
-- ============================================================================
-- 
-- This SQL script creates test transactions with past due dates to test:
-- 1. Automatic status update from pending to delayed
-- 2. Late fee calculation (0.5% per day after 3 consecutive missed payments)
-- 3. Days late calculation
--
-- IMPORTANT: Before running these queries, you need to:
-- 1. Find an approved loan_id from your loan_details table
-- 2. Find the corresponding user_id
-- 3. Calculate the daily EMI amount
-- 4. Replace the placeholders in the queries below
--
-- ============================================================================

-- Step 1: Check if paid_amount column exists (if not, you may need to add it)
-- ============================================================================
SHOW COLUMNS FROM transactions LIKE 'paid_amount';

-- If paid_amount doesn't exist, add it:
-- ALTER TABLE transactions ADD COLUMN paid_amount DECIMAL(15,2) DEFAULT 0 AFTER amount;

-- Step 2: Get your loan details (run this first to get loan_id, user_id, and loan_amount)
-- ============================================================================
SELECT 
    loan_id,
    user_id,
    loan_amount,
    tenure,
    status,
    created_at
FROM loan_details 
WHERE status = 'approved'
ORDER BY created_at DESC
LIMIT 1;

-- Step 3: Calculate daily EMI (use this formula or run in Laravel)
-- Daily EMI = (loan_amount + interest) / (tenure * 30)
-- Example: If loan_amount = 100000, tenure = 6 months
-- Total with interest (15% per 3 months) = 100000 + (100000 * 0.15 * 2) = 130000
-- Daily EMI = 130000 / (6 * 30) = 130000 / 180 = 722.22

-- ============================================================================
-- Step 4: Insert test transactions (REPLACE THE VALUES BELOW)
-- ============================================================================
-- Replace these values:
-- @LOAN_ID: Your loan_id from Step 1 (e.g., 'LON001')
-- @USER_ID: Your user_id from Step 1 (e.g., 1)
-- @DAILY_EMI: Your calculated daily EMI (e.g., 722.22)
-- @TODAY: Today's date in YYYY-MM-DD format (e.g., '2025-11-08')

-- Example: If today is 2025-11-08 and loan_id is 'LON001', user_id is 1, daily_EMI is 722.22

-- Transaction 1: Due 5 days ago (should be delayed, but no late fee yet - needs 3+ consecutive)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 2: Due 10 days ago (should be delayed, no late fee yet)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 3: Due 15 days ago (should be delayed, no late fee yet)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 4: Due 20 days ago (should be delayed, WITH late fee - 4th consecutive)
-- Expected late fee: loan_amount * 0.005 * 1 = (e.g., 100000 * 0.005 * 1 = 500)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 5: Due 25 days ago (should be delayed, WITH late fee - 5th consecutive)
-- Expected late fee: loan_amount * 0.005 * 2 = (e.g., 100000 * 0.005 * 2 = 1000)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 25 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 6: Due 30 days ago (should be delayed, WITH late fee - 6th consecutive)
-- Expected late fee: loan_amount * 0.005 * 3 = (e.g., 100000 * 0.005 * 3 = 1500)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- Transaction 7: Due 35 days ago (should be delayed, WITH late fee - 7th consecutive)
-- Expected late fee: loan_amount * 0.005 * 4 = (e.g., 100000 * 0.005 * 4 = 2000)
INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
VALUES ('LON001', 1, 722.22, DATE_SUB(CURDATE(), INTERVAL 35 DAY), 'pending', 0, 0, 0, NOW(), NOW());

-- ============================================================================
-- ALTERNATIVE: Dynamic SQL query that automatically gets loan details
-- ============================================================================
-- This query will automatically use the first approved loan and calculate daily EMI

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 5 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 10 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 15 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 20 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 25 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 30 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

INSERT INTO transactions (loan_id, user_id, amount, due_date, status, paid_amount, late_fee, days_late, created_at, updated_at)
SELECT 
    ld.loan_id,
    ld.user_id,
    ROUND((ld.loan_amount + (ld.loan_amount * 0.15 * (ld.tenure / 3))) / (ld.tenure * 30), 2) as daily_emi,
    DATE_SUB(CURDATE(), INTERVAL 35 DAY) as due_date,
    'pending' as status,
    0 as paid_amount,
    0 as late_fee,
    0 as days_late,
    NOW() as created_at,
    NOW() as updated_at
FROM loan_details ld
WHERE ld.status = 'approved'
ORDER BY ld.created_at DESC
LIMIT 1;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check inserted transactions
SELECT 
    t.id,
    t.loan_id,
    t.due_date,
    DATEDIFF(CURDATE(), t.due_date) as days_ago,
    t.amount,
    t.status,
    t.late_fee,
    t.days_late,
    ld.loan_amount,
    ROUND(ld.loan_amount * 0.005 * GREATEST(0, (SELECT COUNT(*) FROM transactions t2 
        WHERE t2.loan_id = t.loan_id 
        AND t2.due_date < t.due_date 
        AND t2.status != 'completed'
        ORDER BY t2.due_date DESC) - 3), 2) as expected_late_fee
FROM transactions t
JOIN loan_details ld ON t.loan_id = ld.loan_id
WHERE t.loan_id = (SELECT loan_id FROM loan_details WHERE status = 'approved' ORDER BY created_at DESC LIMIT 1)
ORDER BY t.due_date DESC;

-- ============================================================================
-- CLEANUP (if you want to remove test data)
-- ============================================================================
-- DELETE FROM transactions 
-- WHERE loan_id = 'LON001' 
-- AND due_date < CURDATE() 
-- AND status = 'pending';

-- ============================================================================
-- TESTING INSTRUCTIONS
-- ============================================================================
-- 1. Run the INSERT queries above (use the dynamic SELECT version for automatic calculation)
-- 2. Visit the admin payment page: /admin/payment
-- 3. The system will automatically update pending transactions to 'delayed'
-- 4. Check the transactions - they should show:
--    - Status: 'delayed' (for past due dates)
--    - Days Late: calculated based on due date
--    - Late Fee: ₹0 for first 3 consecutive, then 0.5% per day after
--
-- Expected Late Fee Calculation (for loan_amount = 100000):
--   - Transactions 1-3: ₹0 (first 3 consecutive missed payments)
--   - Transaction 4: ₹500 (1 day after 3rd missed: 100000 * 0.005 * 1)
--   - Transaction 5: ₹1000 (2 days after 3rd missed: 100000 * 0.005 * 2)
--   - Transaction 6: ₹1500 (3 days after 3rd missed: 100000 * 0.005 * 3)
--   - Transaction 7: ₹2000 (4 days after 3rd missed: 100000 * 0.005 * 4)
-- ============================================================================

