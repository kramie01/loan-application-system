document.addEventListener("DOMContentLoaded", function () {
  // Function to convert input to uppercase with exceptions
  function convertToUppercase(input) {
    // Skip certain input types that shouldn't be uppercase
    const skipTypes = ['email', 'password', 'date', 'time', 'datetime-local', 'number', 'tel'];
    
    if (!skipTypes.includes(input.type.toLowerCase())) {
      input.addEventListener('input', function() {
        const cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
      });
    }
  }

  // Convert all existing text inputs to uppercase
  const allInputs = document.querySelectorAll('input, textarea');
  allInputs.forEach(convertToUppercase);

  // Rank toggle logic
  const rank = document.getElementById('rank');
  const otherRankContainer = document.getElementById('otherRankContainer');
  const otherRank = document.getElementById('otherRank');

  rank.addEventListener('change', function () {
    if (this.value === 'Others') {
      otherRankContainer.style.display = 'block';
      otherRank.required = true;
    } else {
      otherRankContainer.style.display = 'none';
      otherRank.value = '';
      otherRank.required = false;
    }
  });

  // Credit card logic
  const container = document.getElementById('creditCardsContainer');
  const addBtn = document.getElementById('addCreditCardBtn');
  let cardIndex = 1;

  addBtn.addEventListener('click', () => {
    const newGroup = document.createElement('div');
    newGroup.classList.add('credit-card-group');

    newGroup.innerHTML = `
      <label for="cardNo_${cardIndex}">Card Number</label>
      <input id="cardNo_${cardIndex}" name="cardNo[]" required />

      <label for="creditCard_${cardIndex}">Credit Card Type</label>
      <input id="creditCard_${cardIndex}" name="creditCard[]" required />

      <label for="creditLimit_${cardIndex}">Credit Limit</label>
      <input id="creditLimit_${cardIndex}" name="creditLimit[]" required />

      <label for="expiryDate_${cardIndex}">Expiry Date</label>
      <input id="expiryDate_${cardIndex}" type="date" name="expiryDate[]" required />

      <button type="button" class="removeCardBtn" title="Remove">×</button>
    `;

    container.appendChild(newGroup);

    // Apply uppercase conversion to newly added inputs
    const newInputs = newGroup.querySelectorAll('input, textarea');
    newInputs.forEach(convertToUppercase);

    cardIndex++;
  });

  // Delegate remove button click
  container.addEventListener('click', function (e) {
    if (e.target.classList.contains('removeCardBtn')) {
      e.target.closest('.credit-card-group').remove();
    }
  });
});