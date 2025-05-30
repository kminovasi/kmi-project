import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js", // File app.blade.php
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
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: [
                "resources/js/app.js", // File app.blade.php
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
