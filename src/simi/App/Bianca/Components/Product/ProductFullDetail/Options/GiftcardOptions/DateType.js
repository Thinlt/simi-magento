import DateOptionType from 'src/simi/App/core/RootComponents/Product/ProductFullDetail/Options/OptionType/Date';

class DateType extends DateOptionType {
    handleChange = (event, date) => {
        this.setState({
            date: date,
        });
        const { key } = this;
        if(date){
            let value = this.formatDate(date);
            if (key) {
                jQuery('form [name="'+key+'"]').val(value);
            }
            if (this.props.inputRef) {
                this.props.inputRef.current.value = value;
            }
        }else{
            if (key) {
                jQuery('form [name="'+key+'"]').val('');
            }
            if (this.props.inputRef) {
                this.props.inputRef.current.value = '';
            }
        }
        if (this.props.onChange) this.props.onChange()
    };
}
export default DateType;