from flask import Flask, render_template, request, jsonify
import datetime
import ephem
from typing import List

app = Flask(__name__)

def years_with_full_moon_on_date(target_month: int, target_day: int,
                                 start_year: int, end_year: int) -> List[int]:
    matching_years = []
    
    for year in range(start_year, end_year + 1):
        try:
            target_date = datetime.date(year, target_month, target_day)
        except ValueError:
            continue
            
        ephem_date = ephem.Date(target_date)
        prev_full = ephem.previous_full_moon(ephem_date)
        next_full = ephem.next_full_moon(ephem_date)
        
        # Convert Ephem dates to Python datetime objects
        prev_dt = prev_full.datetime()
        next_dt = next_full.datetime()
        target_dt = target_date
        
        # Compare only the date parts (year, month, day)
        if (target_dt.year == prev_dt.year and 
            target_dt.month == prev_dt.month and 
            target_dt.day == prev_dt.day):
            matching_years.append(year)
        elif (target_dt.year == next_dt.year and 
              target_dt.month == next_dt.month and 
              target_dt.day == next_dt.day):
            matching_years.append(year)
    
    return matching_years

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/calculate', methods=['POST'])
def calculate():
    data = request.json
    target_month = int(data['month'])
    target_day = int(data['day'])
    start_year = int(data['startYear'])
    end_year = int(data['endYear'])
    
    results = years_with_full_moon_on_date(
        target_month, target_day, start_year, end_year
    )
    
    return jsonify({
        'success': True,
        'results': results,
        'count': len(results)
    })

if __name__ == '__main__':
    app.run(debug=True)