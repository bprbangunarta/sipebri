export type AnalysisRecord = {
    id: number;
    application_code: string;
    full_name: string;
    nik: string;
    requested_amount: number;
    requested_tenor: number;
    interest_rate: string | null;
    usage_type: string | null;
    status: string;
    status_label: string;
    product_label: string | null;
    office_label: string | null;
    supervisor_name: string | null;
    installment_label: string | null;
    installment_period: number;
    survey_note: string | null;
    survey_by: string | null;
    survey_at: string | null;
};

export type Section = { key: string; label: string; icon: string };

export type BusinessRow = {
    id: number;
    type: 'trade' | 'farm' | 'service' | 'other';
    code: string;
    name: string;
    revenue: number;
    expense: number;
    net_profit: number;
    monthly_income: number;
};

export type FinanceMetrics = {
    trade_income: number;
    farm_income: number;
    service_income: number;
    other_income: number;
    business_income: number;
    household_cost: number;
    obligation_cost: number;
    monthly_balance: number;
};

export type Finance = Record<string, string | number | null> & {
    obligations: { name: string; amount: number }[];
    assets: { name: string }[];
    metrics: FinanceMetrics;
    updated_at: string | null;
};

export type FiveGroup = {
    filled: number;
    total: number;
    score: number;
    max: number;
    percent: number;
    grade: string | null;
};

export type FiveC = Record<string, number | null | object | string> & {
    metrics: {
        groups: Record<string, FiveGroup>;
        percent: number;
        grade: string | null;
    };
    updated_at: string | null;
};

export type CollateralRow = {
    collateral_id: number;
    label: string | null;
    document_number: string | null;
    owner_name: string | null;
    cbs_appraisal: number;
    kind: string;
    brand: string;
    vehicle_type: string;
    year: string;
    chassis_number: string;
    engine_number: string;
    plate_number: string;
    color: string;
    land_area: number;
    location: string;
    market_value: number;
    appraisal_value: number;
    notes: string;
};

export type Memorandum = Record<string, string | number | null> & {
    requested_amount: number;
    requested_tenor: number;
    appraisal_total: number;
    monthly_balance: number;
    updated_at: string | null;
};

export type Administration = Record<string, number | string | null> & {
    total: number;
    updated_at: string | null;
};

export type Options = {
    assets: Record<string, string[]>;
    qualitativeChoices: Record<string, string[]>;
    collateralKinds: string[];
    bindings: string[];
};
