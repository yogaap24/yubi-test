const BASE_URL = '';

export const createSalesOrder = async (formData) => {
  const payload = {
    ...formData
  };
  
  try {
    const response = await fetch(`${BASE_URL}/api/sales-orders`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    const result = await response.json();
    if (!response.ok) {
      const errorMsg = result.errors ? JSON.stringify(result.errors) : result.message;
      throw new Error(`Error ${response.status}: ${errorMsg}`);
    }
    return result;
  } catch (error) {
    console.error("API Error - createSalesOrder:", error);
    throw error;
  }
};