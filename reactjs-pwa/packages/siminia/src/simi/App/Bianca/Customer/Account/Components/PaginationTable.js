/* eslint-disable jsx-a11y/no-onchange */
/* eslint-disable prefer-const */
/* eslint-disable jsx-a11y/click-events-have-key-events */
/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
import React from 'react'
import Pagination from 'src/simi/BaseComponents/Pagination'
import Identify from 'src/simi/Helper/Identify'
import Arrow from "src/simi/BaseComponents/Icon/Arrow";

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
        const {setLimit} = this.props;
        setLimit(e.target.value)
    }

    renderDropDown = () => {
        return(
            <select itemType="number" className='dropdown-show-item' onChange={this.changeLimit}>
                <option value={Number(10)}>10</option>
                <option value={20}>20</option>
                <option value={30}>30</option>
            </select>
        )
    }

    renderPageNumber = (total)=> {
        // Logic for displaying page numbers
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
                    className={`page-nums ${active}`}
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
                <li className="icon-page-number start" key={"p-start"}>(</li>
                {obj.state.currentPage === 1 ? 
                    Identify.isRtl() ?
                    <li className={`icon-page-number prev disabled`} key={"p-prev1"}>{nextPageIcon}</li> :
                    <li className={`icon-page-number prev disabled`} key={"p-prev2"}>{prevPageIcon}</li>
                    :
                    Identify.isRtl() ?
                    <li className={`icon-page-number prev`} onClick={()=>this.handleChangePage(false, total)} key={"p-prev3"}>{nextPageIcon}</li>
                    : 
                    <li className={`icon-page-number prev`} onClick={()=>this.handleChangePage(false, total)} key={"p-prev4"}>{prevPageIcon}</li>
                }
                {renderPageNumbers}
                {obj.state.currentPage >= total ? 
                    Identify.isRtl() ?
                    <li className={`icon-page-number next disabled`} key={"p-next1"}>{prevPageIcon}</li> :
                    <li className={`icon-page-number next disabled`} key={"p-next2"}>{nextPageIcon}</li>
                    :
                    Identify.isRtl() ?
                    <li className={`icon-page-number next`} onClick={()=>this.handleChangePage(true, total)} key={"p-next3"}>{prevPageIcon}</li>
                    :
                    <li className={`icon-page-number next`} onClick={()=>this.handleChangePage(true, total)} key={"p-next4"}>{nextPageIcon}</li>
                }
                <li className="icon-page-number end" key={"p-end"}>)</li>
            </ul>
        ):'';
        let {currentPage,limit} = this.state;
        let lastItem = currentPage * limit;
        let firstItem = lastItem - limit+1;
        lastItem = lastItem > totalItem ? totalItem : lastItem;
        let itemsPerPage = (
            <div className="icon-page-number">
                {
                    this.props.showInfoItem &&
                    <span style={{marginRight : 10,fontSize : 16}}>
                        {`${totalItem} ${totalItem > 1 ? 'items':'item'}`}
                    </span>
                }
            </div>
        );
        if (total < 2) return null;
        return (
            <div className="config-page">
                <div className="pagination-info">
                    {itemsPerPage}
                </div>
                {pagesSelection}
            </div>
        )
    }

    renderPagination = () => {
        let {data, currentPage, limit} = this.state;
        if(data.length > 0){
            // Logic for displaying current todos
            const indexOfLastTodo = currentPage * limit;
            const indexOfFirstTodo = indexOfLastTodo - limit;
            const currentReview = data.slice(indexOfFirstTodo, indexOfLastTodo);
            const items = currentReview.map((item, key) => {
                return this.renderItem(item, key);
            });
            let total = data.length;
            return (
                <React.Fragment>
                    <table className='table-striped table-siminia'>
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
