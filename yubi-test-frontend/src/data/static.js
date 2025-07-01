import { v4 as uuidv4 } from 'uuid';

export const customers = [
  { id: 1, name: 'PT. Sukses Selalu' },
  { id: 2, name: 'CV. Jaya Abadi' },
  { id: 3, name: 'Toko Perangkat Keras' },
  { id: 4, name: 'Warung Teknologi' },
  { id: 5, name: 'Global Tech Solution' },
];

export const units = [
  { id: 1, name: 'UNIT' },
  { id: 2, name: 'PIECE' },
  { id: 3, name: 'BOX' },
];

export const products = [
  {
    id: uuidv4(),
    code: 'CPC002',
    name: 'PC Server Gen-X',
    unit_id: 1,
    price_sell: 25000000,
    price_buy: 22000000
  },
  {
    id: uuidv4(),
    code: '0011',
    name: 'Power Distribution Unit 8-port',
    unit_id: 2,
    price_sell: 750000,
    price_buy: 600000
  },
  {
    id: uuidv4(),
    code: 'NB001',
    name: 'Notebook Pro 14 inch',
    unit_id: 1,
    price_sell: 18500000,
    price_buy: 17000000
  },
  {
    id: uuidv4(),
    code: 'MON24',
    name: 'Monitor 24" IPS 144Hz',
    unit_id: 2,
    price_sell: 3200000,
    price_buy: 2800000
  },
  {
    id: uuidv4(),
    code: 'KBD01',
    name: 'Keyboard Mechanical RGB',
    unit_id: 2,
    price_sell: 950000,
    price_buy: 750000
  },
];

export const currencies = [
  { id: 1, name: 'IDR' },
  { id: 2, name: 'USD' },
  { id: 3, name: 'SGD' },
  { id: 4, name: 'EUR' },
  { id: 5, name: 'JPY' },
];

export const orderTypes = [
  { id: 1, name: 'Sales' },
  { id: 2, name: 'Sewa' },
  { id: 3, name: 'Maintenance' },
  { id: 4, name: 'Konsinyasi' },
  { id: 5, name: 'Peminjaman' },
];

export const statuses = [
  { id: 1, name: 'OPEN' },
  { id: 2, name: 'PROCESS' },
  { id: 3, name: 'SHIPPING' },
  { id: 4, 'name': 'CLOSED' },
  { id: 5, name: 'CANCELLED' },
];