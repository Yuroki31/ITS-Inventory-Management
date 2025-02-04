<div id="borrow-history"></div>

<script>
    function updateBorrowHistory() {
        fetch('ajax.php')
            .then(response => response.json())
            .then(data => {
                const historyDiv = document.getElementById('borrow_history');
                historyDiv.innerHTML = data.map(entry => `<p>${entry}</p>`).join('');
            });
    }

    // Update borrow history every 5 seconds
    setInterval(updateBorrowHistory, 5000);
    updateBorrowHistory(); // Initial load
</script>