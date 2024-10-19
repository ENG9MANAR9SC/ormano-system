export interface AdminParams {
  q: string,
  role: string,
  plan: string,
  status: string,
  options: object,
}

export interface AdminProperties {

  id: number,
  name: string,
  number: string,
  email: string,
  //role: string,
  enabled: number,

}
