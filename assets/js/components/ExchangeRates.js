import React, { useState, useEffect } from 'react';
import axios from 'axios';
import useDateFilter from '../hooks/useDateFilter';

const ExchangeRates = () => {
    const { date, setDate } = useDateFilter();

    const [exchangeRates, setExchangeRates] = useState([]);
    const [selectedDate, setSelectedDate] = useState(date || new Date().toISOString().split('T')[0]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        setDate({ date: selectedDate });
        fetchExchangeRates(selectedDate);
    }, [selectedDate]);

    const fetchExchangeRates = async (date) => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/exchange-rates?date=${date}`);
            setExchangeRates(response.data);
            setError(null);
        } catch (error) {
            setExchangeRates([]);
            setError(error.response ? error.response.data.error : 'An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleDateChange = (event) => {
        setSelectedDate(event.target.value);
    };

    return (
        <div>
            <section className="row-section">
                <div className="container">
                    <div className="row mt-5">
                        <div className="col-md-8 offset-md-2">
                            <h2 className="text-center">Exchange Rates</h2>
                        </div>
                    </div>
                    <div className="row mt-5">
                        <div className="col-12">
                            <label className="mr-1" htmlFor="date">Select Date: </label>
                            <input
                                type="date"
                                id="date"
                                value={selectedDate}
                                onChange={handleDateChange}
                                min="2023-01-01"
                                max={new Date().toISOString().split('T')[0]}
                            />
                        </div>
                    </div>
                    <div className="row mt-2">
                        <div className="col-12">
                            {error && <p style={{ color: 'red' }}>{error}</p>}
                            {loading ? (
                                <p>Loading...</p>
                            ) : (
                                <table className="table table-striped bg-white">
                                    <thead className="bg-dark text-white">
                                        <tr>
                                            <th className="align-middle">Currency</th>
                                            <th className="align-middle text-nowrap">Currency Name</th>
                                            <th className="align-middle border-left">NBP Rate (Selected Date)</th>
                                            <th className="align-middle">Buy Rate (Selected Date)</th>
                                            <th className="align-middle">Sell Rate (Selected Date)</th>
                                            <th className="align-middle border-left">NBP Rate (Today)</th>
                                            <th className="align-middle">Buy Rate (Today)</th>
                                            <th className="align-middle">Sell Rate (Today)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {Object.keys(exchangeRates).map((code) => {
                                            const rate = exchangeRates[code];
                                            const isHigher = (rate.selected_date.nbp_rate > rate.today.nbp_rate) ? 'text-success' : (rate.selected_date.nbp_rate < rate.today.nbp_rate) ? 'text-danger' : '';
                                            return (
                                                <tr key={code}>
                                                    <td>{rate.code}</td>
                                                    <td className="text-nowrap">{rate.name}</td>
                                                    <td className={`border-left ${isHigher}`}>{rate.selected_date.nbp_rate}</td>
                                                    <td className={isHigher}>{rate.selected_date.buy_rate}</td>
                                                    <td className={isHigher}>{rate.selected_date.sell_rate}</td>
                                                    <td className="border-left">{rate.today.nbp_rate}</td>
                                                    <td>{rate.today.buy_rate}</td>
                                                    <td>{rate.today.sell_rate}</td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default ExchangeRates;
