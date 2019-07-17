import React from 'react';
import {getReviews} from 'src/simi/Model/Product';
import Loading from 'src/simi/BaseComponents/Loading';
import Identify from 'src/simi/Helper/Identify';
import Pagination from 'src/simi/BaseComponents/Pagination';
import {StaticRate} from 'src/simi/BaseComponents/Rate'
import classes from './reviewList.css';

class ReviewList extends React.Component {
    constructor(props){
        super(props);
        const {product_id} = this.props;
        const api_data = Identify.ApiDataStorage('product_list_review');
        this.state = {
            data : (api_data && api_data instanceof Object && api_data.hasOwnProperty(product_id))?api_data[product_id]:null
        };
    }

    get renderListItem() {
        const {data} = this.state;
        if(data){
            return (
                <div className={classes["list-review-item"]}>
                    <Pagination data={data.reviews} renderItem={this.renderItem} classes={classes}/>
                </div>
            )
        }
        return <div className={classes["text-center"]}>
            {Identify.__('Review is empty')}
        </div>
    };

    renderItem = (item)=>{
        if(item.hasOwnProperty('votes')){
            const rating_votes = item.votes.map((rate, index) => {
                const point = rate.value;
                return (
                   <div className={classes["rating-votes"]} key={index}>
                       <div className={classes["label-rate"]}>{Identify.__(rate.label)}</div>
                       <div className={classes["item-rating"]}><Rate rate={parseInt(point,10)} size={13}/></div>
                   </div>
               )
            });
            const created = (
                        <div className={`${classes["item-created"]} flex`}>
                            <span>{item.created_at}</span>
                            <span style={{margin : '0 5px'}}>{Identify.__('By')}</span>
                            <span>{item.nickname}</span>
                        </div>
                    )
            return(
                <div className={`${classes["review-item"]} ${classes["item"]}`} key={item.review_id}>
                    <div className={`${classes["item-title"]} flex`}>{item.title}</div>
                    <div className={classes["review-item-detail"]}>
                        <div className={classes["item-votes"]}>
                            {rating_votes}
                        </div>
                        <div className={classes["item-review-content"]} >
                            <div className={classes["item-detail"]}>{item.detail}</div>
                            {created}
                        </div>
                    </div>
                    <div className={classes["clearfix"]}></div>
                </div>
            )
        }

        return(
            <div className={`${classes["review-item"]} ${classes["item"]}`} key={item.review_id}>
                <div className={`${classes["item-title"]} flex`}>{item.title}</div>
                <div className={classes["review-item-detail"]}>
                    <div className={classes["item-rate"]}><StaticRate rate={item.rate_points} /></div>
                    <div className={`${classes["item-created"]} flex`} style={{marginLeft : Identify.isRtl() ? 0 : 'auto',marginRight : Identify.isRtl() ? 'auto' : 0}}>
                        <span>{item.created_at}</span>
                        <span style={{margin : '0 5px'}}>By</span>
                        <span>{item.nickname}</span>
                    </div>
                </div>
                <div className={classes["item-detail"]}>{item.detail}</div>
            </div>
        )
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
        console.log(this.state.data)
        if(!this.state.data){
            return (<Loading />);
        }
        const {renderListItem} = this
        return (
            <div>
                <h2 className={classes.reviewlistTitle}>
                    <span>{Identify.__('Customer Reviews')}</span>
                </h2>
                {renderListItem}
            </div>
        )
    }
}
export default ReviewList