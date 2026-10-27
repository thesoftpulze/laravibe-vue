import { Nullable } from '.'

export interface User {
    id: number

    name: string
    email: string

    email_verified_at?: Nullable<string>
}

export interface AdministrationUser {
    id: number

    name: string
    email: string
    is_admin: boolean

    email_verified_at: Nullable<string>
    created_at: string
}

export interface Passkey {
    id: number
    name: string
    authenticator: Nullable<string>
    last_used_at: Nullable<string>
    created_at: string
}
