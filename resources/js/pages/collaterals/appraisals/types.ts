export type Choices = Record<string, string>;

export type Item = {
    key: string;
    label: string;
    type: string;
    options?: Choices | null;
    unit?: string;
    hint?: string;
    format?: 'money' | 'percent' | 'text';
};

export type Row = { key: string; label: string; type?: string; computed?: boolean; format?: Item['format']; options?: Choices | null };

export type Column = { key: string; label: string; type?: string; computed?: boolean; format?: Item['format']; hint?: string };

export type Block = {
    layout: 'fields' | 'checks' | 'ratings' | 'grid' | 'photos';
    items?: Item[];
    rows?: Row[];
    columns?: Column[];
};

export type Section = { key: string; label: string; blocks: Block[] };

export type Computed = Record<string, number | string | null>;

export type Answer = { value: string; note: string };

export type Answers = Record<string, Answer>;

export type Photo = { id: number; url: string; created_by: string };

export type Summary = {
    id: number;
    sequence: number;
    status: 'draft' | 'final';
    purpose: string;
    purpose_label: string;
    effective_date: string | null;
    valid_until: string | null;
    appraiser_name: string | null;
    market_value: number;
    liquidation_value: number;
    decision: string | null;
    decision_label: string | null;
    finalized_at: string | null;
    template: string;
    /** The change of the market value from the previous finalised appraisal (null when there is none to compare with). */
    market_change?: number | null;
    market_change_percent?: number | null;
    liquidation_change?: number | null;
    previous_sequence?: number | null;
};

export type Fact = { label: string; value: string | number; type: 'text' | 'date' | 'integer' };

export type CollateralSummary = { id: number; label: string; facts: Fact[] };
