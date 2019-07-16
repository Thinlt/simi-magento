import React from 'react';
import {getReviews} from 'src/simi/Model/Product';
import Loading from 'src/simi/BaseComponents/Loading';
import Identify from 'src/simi/Helper/Identify';
import ReviewItem from './ReviewItem';
import Pagination from 'src/simi/BaseComponents/Pagination';
import classes from './reviewList.css';

class ReviewList extends React.Component {
    constructor(props){
        super(props);
        let data = null
        const {product_id} = this.props;
        const api_data = Identify.ApiDataStorage('product_list_review');
        if(api_data && api_data instanceof Object && api_data.hasOwnProperty(product_id)){
            data = {data:api_data[product_id]}
        }
        this.state = {
            data : data
        };
    }
    renderItem = (item)=>{
        return <ReviewItem key={Identify.randomString(5)} rates={this.props.rates} data={item}/>
    };

    renderListItem =()=>{
        const {data} = this.state;
        if(data){
            return (
                <div className={classes["list-review-item"]}>
                    <div className={classes["list-review-title"]}>{Identify.__('Customer Reviews')}</div>
                    <Pagination data={data.reviews} renderItem={this.renderItem} />
                </div>
            )
        }
        return <div className={classes["text-center"]}>
            {Identify.__('Review is empty')}
        </div>
    };

    componentDidMount(){
        if(!this.state.data){
            getReviews(this.setData, this.props.product_id)
        }

    }

    setData = (data) => {
        if (data.errors) {
            const errors = data.errors;
            let text = "";
            for (const i in errors) {
                const error = errors[i];
                text += error.message + ' ';
            }
            if (text !== "") {
                Identify.showToastMessage(text);
            }
        } else {
            this.setState({
                data: data,
            });
            const api_data = {};
            api_data[this.props.product_id] = data
            Identify.ApiDataStorage('product_list_review','update',api_data)
        }
    }

    render (){
        if(!this.state.data){
            return (<Loading />);
        }
        return this.renderListItem();
    }
}
export default ReviewList