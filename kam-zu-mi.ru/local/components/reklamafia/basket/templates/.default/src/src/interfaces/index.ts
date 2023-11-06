export interface IPickupPoint {
  id: string;
  name: string;
}

export interface IAppProps {
  basket: Array<{
    id: string;
    quantity: number;
    price: number;
  }>;
  deliveryCities: Array<{
    id: string;
    name: string;
  }>;
  pickupPoints: Array<IPickupPoint>;
}

export interface IDeliveryInfo {
  name: string;
  phone: string;
  comment: string;
  guestCount: number;
  pickup?: {
    pickupPointId: string;
  } | null;
  courier?: {
    city: string;
    street: string;
    house: string;
    housing: string;
    apartment: string;
  } | null;
}
