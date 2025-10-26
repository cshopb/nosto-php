import {InertiaLinkProps} from '@inertiajs/vue3';
import type {LucideIcon} from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface CurrencyItem {
    code: string,
    numericCode: number,
    decimalDigits: number,
    name: string,
    active: boolean,
}

export interface CurrencyArrayItem {
    [key: string]: CurrencyItem;
}

export interface CurrencyRateResponse {
    baseCurrency: CurrencyItem;
    quoteCurrency: CurrencyItem;
    quote: number;
    date: Date;
}

export interface CurrencyRateRequest {
    baseCurrency: string;
    quoteCurrency: string;
}

export interface BackendException {
    code: number,
    message: string,
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
