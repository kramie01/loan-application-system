document.addEventListener("DOMContentLoaded", function () {
  // For Loan Purpose
  const loanPurpose = document.getElementById('loanPurpose');
  const otherPurposeContainer = document.getElementById('otherPurposeContainer');
  const otherLoanPurpose = document.getElementById('otherLoanPurpose');

  loanPurpose.addEventListener('change', function () {
    if (this.value === 'Others') {
      otherPurposeContainer.style.display = 'block';
      otherLoanPurpose.required = true;
    } else {
      otherPurposeContainer.style.display = 'none';
      otherLoanPurpose.value = '';
      otherLoanPurpose.required = false;
    }
  });
});