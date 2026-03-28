export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    email_verified_at?: string;
    assinatura_id?: number;
    assinatura?: any;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    trial_info?: {
        is_trial: boolean;
        days_remaining: number;
    };
};
