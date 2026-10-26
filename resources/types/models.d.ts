import { Nullable } from '.'

export interface User {
    id: number

    name: string
    email: string

    email_verified_at?: Nullable<string>
}
