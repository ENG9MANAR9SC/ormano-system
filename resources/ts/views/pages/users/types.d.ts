export interface UserParams {
  q: string,
  role: string,
  plan: string,
  status: string,
  options: object,
}

export interface UserProperties {
  // id: number
  // fullName: string
  // company: string
  // role: string
  // country: string
  // contact: string
  // email: string
  // currentPlan: string
  // status: string
  // billing:string
  // avatar: string

  id: number,
  full_name: string,
  phone_number: string,
  gender: number,
  avatar: string,
  email: string,
  birth_date: string,
  active: number,
  balance: number,
  email_verified_at: string,
  password: string,
  notes: string,
  created_at: string,
  updated_at: string,
  occupation: string,
  civil_status: string,
  age: number,
  gender_title: string,
  address: string,
}
