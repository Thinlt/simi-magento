/* eslint-disable prefer-const */
/* eslint-disable jsx-a11y/click-events-have-key-events */
/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
import React from 'react'
import Pagination from 'src/simi/BaseComponents/Pagination'
import Identify from 'src/simi/Helper/Identify'
import Arrow from "src/simi/BaseComponents/Icon/Arrow";
import DropDown from 'src/simi/BaseComponents/Dropdownoption';

class PaginationTable extends Pagination {
    constructor(props) {
        super(props)
        this.startPage = 1;
        this.endPage = this.startPage + 2;
    }

    renderColumnTitle = () => {
        let data = this.props.cols;
        if(data.length > 0){
            let columns = data.map((item, index)=>{
                return <th key={index} width={item.width?item.width: ''} >{Identify.__(item.title)}</th>
            });
            return (
                <thead>
                    <tr>
                        {columns}
                    </tr>
                </thead>
            ) 
            
        }
    }

    componentDidUpdate(prevProps){
        if(this.props.limit !== prevProps.limit){
            this.setState({limit: this.props.limit})
        }
    }

    handleChangePage =(next = true, total)=>{
        let currentPage = next ? (this.state.currentPage === total?this.state.currentPage: this.state.currentPage + 1) : (this.state.currentPage> 1 ? this.state.currentPage - 1: this.state.currentPage);
        if(currentPage > this.endPage){
            this.startPage = this.startPage + 1;
            this.endPage = this.endPage + 1;
        }else if (currentPage < this.startPage){
            this.startPage = this.startPage - 1;
            this.endPage = this.endPage - 1;
        }
        this.setState({
            currentPage : currentPage
        })
    }

    changeLimit = (e) => {
        const {setLimit, setTitle} = this.props;
        setLimit(e.target.value)
        setTitle(e.target.value)
    }

    renderDropDown = () => {
        const {classes, title} = this.props;
        return(
            <div>
            <DropDown title={`${title} items`} className={classes['dropdown-show-item']}>
                <ul>
                    <li onClick={this.changeLimit} value={10}>10</li>
                    <li onClick={this.changeLimit} value={20}>20</li>
                </ul>
            </DropDown>

            </div>
        )
    }

    renderPageNumber = (total)=> {
        // Logic for displaying page numbers
        const { classes } = this.props;
        if(!this.props.showPageNumber) return null;
        const pageNumbers = [];
        let totalItem = total;
        total =  Math.ceil(total / this.state.limit);
        let endpage = this.endPage > total ? total : this.endPage
        for (let i = this.startPage; i <= endpage; i++) {
            pageNumbers.push(i);
        }
        let obj = this;
        const renderPageNumbers = pageNumbers.map(number => {
            let active = number === obj.state.currentPage ? 'active': '';
            return (
                // eslint-disable-next-line jsx-a11y/click-events-have-key-events
                <li
                    key={number}
                    id={number}
                    onClick={(e)=>this.changePage(e)}
                    className={`${classes['page-nums']} ${classes[active]}`}
                >
                    {number}
                </li>
            );
        });
        let option_limit = [];
        if (this.props.itemsPerPageOptions)
        {
            this.props.itemsPerPageOptions.map((item, index) => {
                    option_limit.push(<option key={index} value={item} >{item}</option>);
                    return null 
                }
            );
        }
        let nextPageIcon = <Arrow style={{width: 20, height: 20, transform: 'rotate(90deg)'}}/>;
        let prevPageIcon = <Arrow style={{width: 20, height: 20, transform: 'rotate(-90deg)'}}/>;

        let pagesSelection = (total>1)?(
            <ul id="page-numbers" style={{
                border : 'none',
                padding : 0,
                display : 'flex',
                alignItems : 'center',
                fontSize : 14,
            }}>
                <li className={classes["icon-page-number"]} onClick={()=>this.handleChangePage(false, total)}>{prevPageIcon}</li>
                {renderPageNumbers}
                <li className={classes["icon-page-number"]} onClick={()=>this.handleChangePage(true, total)}>{nextPageIcon}</li>
            </ul>
        ):'';
        let {currentPage,limit} = this.state;
        let lastItem = currentPage * limit;
        let firstItem = lastItem - limit+1;
        lastItem = lastItem > totalItem ? totalItem : lastItem;
        let itemsPerPage = (
            <div className={classes["icon-page-number"]}>
                {
                    this.props.showInfoItem &&
                    <span style={{marginRight : 10,fontSize : 16}}>
                        {Identify.__('%a - %b of %c').replace('%a', firstItem).replace('%b', lastItem).replace('%c', totalItem)}
                    </span>
                }
            </div>
        );
        return (
            <div className={classes["config-page"]}
                 style={{
                     display : 'flex',
                     alignItems : 'baseline',
                     justifyContent : 'space-between',
                     clear: 'both'
                 }}
            >
                {itemsPerPage}
                {this.renderDropDown()}
                {pagesSelection}
            </div>
        )
    }

    renderPagination = () => {
        let {data, currentPage, limit} = this.state;
        const {classes} = this.props;
        if(data.length > 0){
            // Logic for displaying current todos
            const indexOfLastTodo = currentPage * limit;
            const indexOfFirstTodo = indexOfLastTodo - limit;
            const currentReview = data.slice(indexOfFirstTodo, indexOfLastTodo);
            let obj = this;
            const items = currentReview.map((item, key) => {
                return obj.renderItem(item, key);
            });
            let total = data.length;
            return (
                <React.Fragment>
                    <table className={`col-xs-12 ${classes["table-striped"]} ${classes["table-siminia"]}`}>
                        {this.renderColumnTitle()}
                        <tbody>{items}</tbody>
                    </table>
                    {this.renderPageNumber(total)}
                </React.Fragment>
            )
        }
        return <div></div>
    }

    render() {
        return this.renderPagination();
    }
}

export default PaginationTable
