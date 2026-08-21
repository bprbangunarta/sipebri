/**
 * Cermin rumus backend (App\Models\AnalysisBusiness::metrics dan
 * App\Models\AnalysisSheet::metrics) supaya angka hasil hitung tampil
 * langsung saat analis mengetik. Sumber kebenaran tetap di backend.
 */
const num = (value) => (value === '' || value === null || value === undefined ? 0 : Number(value));

export const HARVEST_MONTHS = 6;

export const TRADE_COSTS = [
    { key: 'transport_cost', label: 'Transportasi (Harian)' },
    { key: 'employee_cost', label: 'Pegawai (Harian)' },
    { key: 'retribution_cost', label: 'Retribusi (Harian)' },
    { key: 'unload_cost', label: 'Bongkar Muat (Harian)' },
    { key: 'gatel_cost', label: 'Gatel (Harian)' },
    { key: 'rent_cost', label: 'Sewa Tempat (Harian)' },
];

export const FARM_COSTS = [
    { key: 'cost_land', label: 'Pengolahan Tanah' },
    { key: 'cost_seed', label: 'Biaya Bibit' },
    { key: 'cost_fertilizer', label: 'Biaya Pupuk' },
    { key: 'cost_pesticide', label: 'Biaya Pestisida' },
    { key: 'cost_labor', label: 'Tenaga Kerja' },
    { key: 'cost_irrigation', label: 'Biaya Pengairan' },
    { key: 'cost_harvest', label: 'Biaya Panen' },
    { key: 'cost_sharecropper', label: 'Biaya Penggarap' },
    { key: 'cost_tax', label: 'Biaya Pajak' },
    { key: 'cost_village', label: 'Iuran Desa' },
    { key: 'cost_amortization', label: 'Biaya Amortisasi' },
    { key: 'cost_other_bank', label: 'Pinjaman Bank Lain' },
];

export const HOUSEHOLD_COSTS = [
    { key: 'cost_staple', label: 'Konsumsi Pokok' },
    { key: 'cost_education', label: 'Pendidikan' },
    { key: 'cost_children', label: 'Jajan Anak' },
    { key: 'cost_cigarette', label: 'Rokok' },
    { key: 'cost_health', label: 'Kesehatan' },
    { key: 'cost_gatel', label: 'Gatel' },
    { key: 'cost_social', label: 'Sumbangan Sosial' },
];

export const ASSET_LABELS = {
    asset_house: 'Rumah',
    asset_car: 'Mobil',
    asset_motorcycle: 'Motor',
    asset_computer: 'Komputer',
    asset_washer: 'Mesin Cuci',
    asset_tv: 'Televisi',
    asset_chair: 'Kursi Tamu',
    asset_cabinet: 'Lemari Panjang',
};

export const tradeMetrics = (form, goods) => {
    const totalBuy = goods.reduce((t, r) => t + num(r.price), 0);
    const totalSell = goods.reduce((t, r) => t + num(r.sell_price), 0);
    const totalProfit = totalSell - totalBuy;
    // Persentase dibulatkan 2 desimal dulu (cermin backend).
    const margin = totalBuy > 0 ? Math.round((totalProfit / totalBuy) * 10000) / 100 : 0;

    const dailyRevenue = Math.round(num(form.daily_purchase) * (1 + margin / 100));
    const dailyProfit = dailyRevenue - num(form.cost_of_goods);
    const dailyCost = TRADE_COSTS.reduce((t, c) => t + num(form[c.key]), 0);
    const monthlyProfit = dailyProfit * 30;
    const monthlyCost = dailyCost * 30;

    return {
        total_buy: totalBuy,
        total_sell: totalSell,
        total_profit: totalProfit,
        total_stock: goods.reduce((t, r) => t + num(r.qty), 0),
        margin_percent: margin,
        daily_revenue: dailyRevenue,
        daily_profit: dailyProfit,
        daily_cost: dailyCost,
        monthly_profit: monthlyProfit,
        monthly_cost: monthlyCost,
        net_profit: monthlyProfit - monthlyCost + num(form.projection_addition),
    };
};

export const farmMetrics = (form) => {
    const harvestIncome = Math.round(num(form.harvest_kw) * num(form.price_per_kw));
    const totalCost = FARM_COSTS.reduce((t, c) => t + num(form[c.key]), 0);
    const net = harvestIncome - totalCost;

    return {
        total_area: num(form.area_own) + num(form.area_rent) + num(form.area_pawn),
        harvest_income: harvestIncome,
        total_cost: totalCost,
        net_profit: net,
        monthly_income: Math.round(
            (net + num(form.addition_result) - num(form.principal_installment) - num(form.other_bank_loan)) /
                HARVEST_MONTHS,
        ),
    };
};

export const serviceMetrics = (form) => {
    const income = num(form.service_income);
    const expense = num(form.vehicle_tax) + num(form.other_expense);

    return { total_income: income, total_expense: expense, net_profit: income - expense };
};

export const otherMetrics = (form, materials, incomes, expenses) => {
    const businessIncome = incomes.reduce((t, r) => t + num(r.price), 0);
    const operational = expenses.reduce((t, r) => t + num(r.price), 0);
    const material = materials.reduce((t, r) => t + num(r.qty) * num(r.price), 0);

    return {
        business_income: businessIncome,
        operational_cost: operational,
        material_cost: material,
        net_profit: businessIncome - operational - material + num(form.projection_addition),
    };
};

export const financeMetrics = (form, obligations, incomeByType) => {
    const household = HOUSEHOLD_COSTS.reduce((t, c) => t + num(form[c.key]), 0);
    const obligation = obligations.reduce((t, r) => t + num(r.amount), 0);
    const businessIncome =
        num(incomeByType.trade_income) +
        num(incomeByType.farm_income) +
        num(incomeByType.service_income) +
        num(incomeByType.other_income);

    return {
        household_cost: household,
        obligation_cost: obligation,
        business_income: businessIncome,
        monthly_balance: businessIncome - household - obligation,
    };
};
