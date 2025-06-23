import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css", // File app.blade.php
                "resources/css/detailCompanyChart.css",
                "resources/js/app.js", // File app.blade.php
                "resources/js/benefitChart.js", // File home.blade.php
                "resources/js/cementInnovationsChart.js", // File cement-innovation-chart.blade.php
                "resources/js/exportTotalFinancialBenefitByOrganizationChart.js",
                "resources/js/exportTotalInnovatorByOrganization.js",
                "resources/js/exportTotalPotentialBenefitByOrganizationChart.js",
                "resources/js/exportTotalTeamByOrganization.js",
                "resources/js/financialBenefitChartCompanies.js",
                "resources/js/innovatorChart.js",
                "resources/js/nonCementInnovationsChart.js", // File non-cement-innovation-chart.blade.php
                "resources/js/semenChart.js",
                "resources/js/totalBenefit.js",
                "resources/js/totalBenefitChart.js",
                "resources/js/totalFinancialBenefitByOrganizationChart.js",
                "resources/js/totalInnovatorByOrganizationChart.js",
                "resources/js/totalInnovatorChart.js",
                "resources/js/totalInnovatorChartInternal.js",
                "resources/js/totalPotentialBenefitByOrganizationChart.js",
                "resources/js/totalTeamChart.js", // File total-team-chart.blade.php
                "resources/js/totalTeamChartInternal.js",
                "resources/js/totalTeamByOrganizationChart.js",
                "resources/js/company/companyDashboardChart.js",
                "resources/js/company/detailCompanyChart.js",
                "resources/js/company/exportExcel.js",
                "resources/js/company/exportPdf.js",
                "resources/js/company/exportTotalInnovatorWithGender.js",
                "resources/js/company/totalInnovatorWithGenderChart.js",
                "resources/js/event/exportTotalBenefitCompanyChart.js",
                "resources/js/event/exportTotalInnovatorCategories.js",
                "resources/js/event/exportTotalInnovatorEventChart.js",
                "resources/js/event/exportTotalInnovatorStages.js",
                "resources/js/event/exportTotalPotentialBenefitCompanyChart.js",
                "resources/js/event/totalBenefitCompanyChart.js",
                "resources/js/event/totalInnovatorCategories.js",
                "resources/js/event/totalInnovatorEventChart.js",
                "resources/js/event/totalInnovatorStages.js",
                "resources/js/event/totalPotentialBenefitCompanyChart.js",
                "resources/js/event/totalTeamCompanyChart.js",
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: [
                "resources/js/app.js",
                "resources/css/detailCompanyChart.css",
                "resources/css/app.css", // File app.blade.php
                "resources/js/benefitChart.js", // File home.blade.php
                "resources/js/cementInnovationsChart.js", // File cement-innovation-chart.blade.php
                "resources/js/exportTotalFinancialBenefitByOrganizationChart.js",
                "resources/js/exportTotalInnovatorByOrganization.js",
                "resources/js/exportTotalPotentialBenefitByOrganizationChart.js",
                "resources/js/exportTotalTeamByOrganization.js",
                "resources/js/financialBenefitChartCompanies.js",
                "resources/js/innovatorChart.js",
                "resources/js/nonCementInnovationsChart.js", // File non-cement-innovation-chart.blade.php
                "resources/js/semenChart.js",
                "resources/js/totalBenefit.js",
                "resources/js/totalBenefitChart.js",
                "resources/js/totalFinancialBenefitByOrganizationChart.js",
                "resources/js/totalInnovatorByOrganizationChart.js",
                "resources/js/totalInnovatorChart.js",
                "resources/js/totalInnovatorChartInternal.js",
                "resources/js/totalPotentialBenefitByOrganizationChart.js",
                "resources/js/totalTeamChart.js", // File total-team-chart.blade.php
                "resources/js/totalTeamChartInternal.js",
                "resources/js/totalTeamByOrganizationChart.js",
                "resources/js/company/companyDashboardChart.js",
                "resources/js/company/detailCompanyChart.js",
                "resources/js/company/exportExcel.js",
                "resources/js/company/exportPdf.js",
                "resources/js/company/exportTotalInnovatorWithGender.js",
                "resources/js/company/totalInnovatorWithGenderChart.js",
                "resources/js/event/exportTotalBenefitCompanyChart.js",
                "resources/js/event/exportTotalInnovatorCategories.js",
                "resources/js/event/exportTotalInnovatorEventChart.js",
                "resources/js/event/exportTotalInnovatorStages.js",
                "resources/js/event/exportTotalPotentialBenefitCompanyChart.js",
                "resources/js/event/totalBenefitCompanyChart.js",
                "resources/js/event/totalInnovatorCategories.js",
                "resources/js/event/totalInnovatorEventChart.js",
                "resources/js/event/totalInnovatorStages.js",
                "resources/js/event/totalPotentialBenefitCompanyChart.js",
                "resources/js/event/totalTeamCompanyChart.js",
            ],
        },
    },
    base: '/build/',
    optimizeDeps: {
        include: [
            "chartjs-plugin-trendline",
            'chartjs-plugin-autocolors'
        ],
    },
});
