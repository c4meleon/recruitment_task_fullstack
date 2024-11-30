import { useCallback } from "react";
import { useLocation, useHistory } from "react-router-dom";

const useDateFilter = () => {
  const location = useLocation();
  const history = useHistory();
  const searchParams = new URLSearchParams(location.search);
  const date = searchParams.get("date");

  const setDate = useCallback((filters) => {
    const newSearchParams = new URLSearchParams(location.search);
    if (filters.date !== undefined) {
      newSearchParams.set("date", filters.date);
    }
    history.push({ search: newSearchParams.toString() });
  }, [location.search, history]);

  return {
    date,
    setDate,
  };
};

export default useDateFilter;
