/**
 * Mirror of the worksheet formulas of the server (App\Models\AnalysisBusiness::metrics and AnalysisFinance::metrics), so the
 * figures show while typing. The server is the source of truth: after a save the page shows its figures.
 */
const num = (value: unknown): number => {
    const n = Number(value);

    return Number.isFinite(n) ? n : 0;
};

/** PHP's round(): halves go away from zero. */
const round = (value: number, decimals = 0): number => {
    const factor = 10 ** decimals;
    const scaled = Math.abs(value) * factor;
    const result = Math.round(Number(scaled.toPrecision(15))) / factor;

    return value < 0 ? -result : result;
};

export const HARVEST_MONTHS = 6;

export const TRADE_COSTS = [
    { key: 'transport_cost', label: 'Transportasi (Harian)' },
    { key: 'employee_cost', label: 'Pegawai (Harian)' },
    { key: 'retribution_cost', label: 'Retribusi (Harian)' },
    { key: 'unload_cost', label: 'Bongkar Muat (Harian)' },
    { key: 'gatel_cost', label: 'Gatel (Harian)' },
    { key: 'rent_cost', label: 'Sewa Tempat (Harian)' },
] as const;

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
] as const;

export const HOUSEHOLD_COSTS = [
    { key: 'cost_staple', label: 'Konsumsi Pokok' },
    { key: 'cost_education', label: 'Pendidikan' },
    { key: 'cost_children', label: 'Jajan Anak' },
    { key: 'cost_cigarette', label: 'Rokok' },
    { key: 'cost_health', label: 'Kesehatan' },
    { key: 'cost_gatel', label: 'Gatel' },
    { key: 'cost_social', label: 'Sumbangan Sosial' },
] as const;

export const ASSET_LABELS: Record<string, string> = {
    asset_house: 'Rumah',
    asset_car: 'Mobil',
    asset_motorcycle: 'Motor',
    asset_computer: 'Komputer',
    asset_washer: 'Mesin Cuci',
    asset_tv: 'Televisi',
    asset_chair: 'Kursi Tamu',
    asset_cabinet: 'Lemari Panjang',
};

type Form = Record<string, unknown>;
type Item = { qty?: unknown; price?: unknown; sell_price?: unknown };

export const tradeMetrics = (form: Form, goods: Item[]) => {
    const totalBuy = goods.reduce((t, r) => t + num(r.price), 0);
    const totalSell = goods.reduce((t, r) => t + num(r.sell_price), 0);
    const totalProfit = totalSell - totalBuy;
    const margin = totalBuy > 0 ? round((totalProfit / totalBuy) * 100, 2) : 0;

    const dailyRevenue = round(num(form.daily_purchase) * (1 + margin / 100));
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

export const farmMetrics = (
    form: Form,
    application: {
        requested_amount: number;
        requested_tenor: number;
        installment_label: string | null;
        installment_period: number;
    },
) => {
    const harvestIncome = round(
        num(form.harvest_quintals) * num(form.price_per_quintal),
    );
    const totalCost = FARM_COSTS.reduce((t, c) => t + num(form[c.key]), 0);
    const net = harvestIncome - totalCost;

    const tenor = num(application.requested_tenor);
    // No installment system at all: one harvest. A period of 0 (bullet): the whole tenor.
    const period =
        application.installment_label === null
            ? HARVEST_MONTHS
            : application.installment_period > 0
              ? application.installment_period
              : tenor;
    const terms = period > 0 ? tenor / period : 0;

    const principal =
        terms > 0 ? round(num(application.requested_amount) / terms) : 0;
    const afterPrincipal = net - principal;

    return {
        total_area:
            num(form.area_own) + num(form.area_rent) + num(form.area_pawn),
        harvest_income: harvestIncome,
        total_cost: totalCost,
        net_profit: net,
        installment_period: period,
        principal_installment: principal,
        after_principal: afterPrincipal,
        other_bank_loan: num(form.cost_other_bank),
        monthly_income:
            period > 0
                ? Math.floor(afterPrincipal / period) +
                  num(form.addition_result)
                : 0,
    };
};

export const serviceMetrics = (form: Form) => {
    const income = num(form.service_income);
    const expense = num(form.vehicle_tax) + num(form.other_expense);

    return {
        total_income: income,
        total_expense: expense,
        net_profit: income - expense,
    };
};

export const otherMetrics = (
    form: Form,
    materials: Item[],
    incomes: Item[],
    expenses: Item[],
) => {
    const businessIncome = incomes.reduce((t, r) => t + num(r.price), 0);
    const operational = expenses.reduce((t, r) => t + num(r.price), 0);
    const material = round(
        materials.reduce((t, r) => t + num(r.qty) * num(r.price), 0),
    );

    return {
        business_income: businessIncome,
        operational_cost: operational,
        material_cost: material,
        net_profit:
            businessIncome -
            operational -
            material +
            num(form.projection_addition),
    };
};

export const financeMetrics = (
    form: Form,
    obligations: { amount?: unknown }[],
    incomeByType: {
        trade_income: number;
        farm_income: number;
        service_income: number;
        other_income: number;
    },
) => {
    const household = HOUSEHOLD_COSTS.reduce((t, c) => t + num(form[c.key]), 0);
    const obligation = obligations.reduce((t, r) => t + num(r.amount), 0);
    const businessIncome =
        incomeByType.trade_income +
        incomeByType.farm_income +
        incomeByType.service_income +
        incomeByType.other_income;

    return {
        household_cost: household,
        obligation_cost: obligation,
        business_income: businessIncome,
        monthly_balance: businessIncome - household - obligation,
    };
};
