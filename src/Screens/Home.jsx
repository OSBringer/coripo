import {React,useState,useEffect,useMemo} from 'react';
import { useTable,useSortBy } from 'react-table';
import "./Home.css";
import Table from '../Components/Table';
import axios from 'axios';
function Home() {
    const [inputName, setInputName] = useState('');
    const [inputCin, setInputCin] = useState(0);
    const [showError,setShowError]= useState(false);
    const [dataArray3, setDataArray] = useState([[{cin:' ',name:' ',address:' '}]]);
    const [companiesArray,setCompaniesArray]= useState([]);

    useEffect(() => {
        setDataArray(()=>[
            companiesArray.map(obj =>{
                return{
                    cin:obj['cin'],
                    name:obj['name'],
                    address:obj['address'],
                }
            })
        ])
    }, [companiesArray])

    const handleFormSubmit = e => {
        e.preventDefault();
        inputName.length!==0  ? setInputName(inputName):setInputCin('');
        const params= {
            cin:inputCin.length && inputCin,
            name:inputName
        }
        axios.get('http://localhost:8000/Ares/get', {
            params:params
          })
          .then(result => {
              setCompaniesArray(result.data)
          })
          .catch(error => console.log(error));
    };

    const handleSave = (data) => {
        const headers = {
            'Content-Type': 'application/json',
        }
        axios.post('http://localhost:8000/Ares/',data)
          .then(result => {
              console.log(result)
              if(result.status === 200){
                alert("Data uložené");
              }
          })
          .catch(error => console.log(error));
    }

    const data = dataArray3[0];
      const columns = useMemo(
        () => [
          {
            Header: 'IČO',
            accessor: 'cin', // accessor is the "key" in the data
          },
          {
            Header: 'Meno',
            accessor: 'name',
          },
          {
            Header: 'Adresa',
            accessor: 'address',
          },
        ],
        []
      )
    return (
        <div>

            <form  action="/action_page.php">
            <label>IČO</label>
            {showError ? <span style={{color:"red"}}>prosím zadajte validne číslo</span>:<></>}
            <input 
                type="number"
                id="ico"
                name="ico"
                placeholder="IČO" 
                pattern="[0-9]*"
                onKeyPress={(event) => {
                    if (!/[^-]*[0-9]/.test(event.key)) {
                      event.preventDefault();
                      setShowError(true)
                    }
                  }}
                onChange={(e) =>{
                        setInputCin((v) => (e.target.validity.valid ? e.target.value : v));
                        setShowError(false);
                    }
                }
            />
            <label>Meno</label>
            <input 
                type="text"
                id="lname" 
                name="lastname" 
                placeholder="Meno firmy"
                onChange={(e) =>
                    setInputName((v) => (e.target.validity.valid ? e.target.value : v))
                }
            />
            <input
                type="submit"
                value="Hladať"
                onClick={e => handleFormSubmit(e)}
            />
            </form>

            <Table columns={columns} data={data} handleSave={handleSave}/>


        </div>
    )
}

export default Home