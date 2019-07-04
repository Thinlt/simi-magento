import React from 'react';
import Abstract from './Abstract';
import DatePicker from 'material-ui/DatePicker';
import Identify from "src/simi/Helper/Identify";
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
const muiTheme = getMuiTheme({});
class DateField extends Abstract {

    constructor(props){
        super(props);
        this.state = {
            date : null
        }
    }

    handleChange = (event, date) => {
        this.setState({
            date: date,
        });
        if(date){
            let key = this.key;
            let value = this.convertDate(date);
            if(this.props.datetime){
                let datetime = this.props.parent.selected[key];
                if(datetime instanceof Object){
                    value = {...datetime,...value};
                }
            }
            this.props.parent.updateOptions(key,value);
        }else{
            this.deleteSelected(this.key);
        }
    };

    convertDate = (date) => {
        let d = date.getDate();
        let m = date.getMonth() + 1;
        m = m < 10 ? "0"+m : m;
        let y = date.getFullYear();
        return {
            year : y,
            month : parseInt(m,10),
            day : d
        }
    };

    formatDate = (date) =>{
        let m = date.getMonth() + 1;
        m = m < 10 ? "0"+m : m;
        if(Identify.isRtl()){
            date = date.getFullYear() + '/' + m + '/' + date.getDate() ;
            return date;
        }
        date = date.getDate() + '/' + m + '/' + date.getFullYear()
        return date;
    };

    renderDate = ()=> {
        let text = Identify.isRtl() ? 'yyyy/mm/dd' : 'dd/mm/yyyy';
        return (
            <MuiThemeProvider muiTheme={muiTheme}>
                <DatePicker
                    className="date-picker"
                    hintText={<div className="flex"><span>{Identify.__('Select date')}</span> <span>: {text}</span></div>}
                    value={this.state.date}
                    minDate={new Date()}
                    mode={window.innerWidth < 768 ? 'portrait' : "landscape"}
                    onChange={this.handleChange}
                    formatDate={this.formatDate}
                    textFieldStyle={{
                        fontFamily : 'Montserrat, sans-serif',
                        color : 'rgba(0, 0, 0, 0.87)'
                    }}
                />
            </MuiThemeProvider>

        )
    }

    render(){
        return this.renderDate();
    }
}
export default DateField;