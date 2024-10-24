document.addEventListener("DOMContentLoaded", function () {
    const companyCards = document.querySelectorAll(".company-card");
    companyCards.forEach((card) => {
        card.addEventListener("click", function () {
            const companyId = this.getAttribute("data-company-id");
            const companyCode = this.getAttribute("data-company-code");
            window.location.href = `/detail-company-chart/${companyId}`;
        });
    });
});
