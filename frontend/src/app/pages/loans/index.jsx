// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, CheckCircleIcon, EyeIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";
import dayjs from "dayjs";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { loansAPI } from "services/api";

// ----------------------------------------------------------------------

export default function LoansList() {
 const [loans, setLoans] = useState([]);
 const [loading, setLoading] = useState(true);
 const [search, setSearch] = useState("");
 const [statusFilter, setStatusFilter] = useState("all");

 const fetchLoans = useCallback(async () => {
   try {
     setLoading(true);
     const params = {
       search,
       per_page: 25,
     };
     
     if (statusFilter !== "all") {
       params.status = statusFilter;
     }

     const response = await loansAPI.getAll(params);
     const data = response.data?.data || [];
     setLoans(data);
   } catch (error) {
     console.error("Error fetching loans:", error);
     toast.error("Failed to load loans");
   } finally {
     setLoading(false);
   }
 }, [search, statusFilter]);

  useEffect(() => {
    fetchLoans();
  }, [fetchLoans]);


 const handleCompleteLoan = async (id) => {
   if (!window.confirm("Mark this loan as completed?")) {
     return;
   }

   try {
     await loansAPI.complete(id);
     toast.success("Loan marked as completed");
     fetchLoans();
   } catch (error) {
     console.error("Error completing loan:", error);
     toast.error("Failed to complete loan");
   }
 };

 const formatCurrency = (amount) => {
   return new Intl.NumberFormat("en-NG", {
     style: "currency",
     currency: "NGN",
   }).format(amount);
 };

 return (
   <Page title="Loans">
     <div className="space-y-5">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div>
           <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
             Loans Management
           </h2>
           <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
             Manage member loans
           </p>
         </div>
         <div className="flex gap-2">
           <Link to="/loans/active">
             <Button variant="outlined" color="primary">
               Active Loans
             </Button>
           </Link>
           <Link to="/loans/create">
             <Button color="primary" className="gap-2">
               <PlusIcon className="size-5" />
               New Loan
             </Button>
           </Link>
         </div>
       </div>

       {/* Filters */}
       <Card className="p-5">
         <div className="flex gap-3">
           <div className="flex-1">
             <Input
               placeholder="Search by Coop ID..."
               value={search}
               onChange={(e) => setSearch(e.target.value)}
               prefix={
                 <MagnifyingGlassIcon className="size-5 text-gray-400" />
               }
             />
           </div>
           <select
             value={statusFilter}
             onChange={(e) => setStatusFilter(e.target.value)}
             className="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-dark-500 dark:bg-dark-700"
           >
             <option value="all">All Status</option>
             <option value="1">Active</option>
             <option value="0">Completed</option>
           </select>
         </div>
       </Card>

       {/* Loans Table */}
       <Card className="overflow-hidden">
         <div className="overflow-x-auto">
           <table className="w-full">
             <thead className="bg-gray-50 dark:bg-dark-600">
               <tr>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Coop ID
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Amount
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Date
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Repayment Date
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Status
                 </th>
                 <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Actions
                 </th>
               </tr>
             </thead>
             <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
               {loading ? (
                 <tr>
                   <td colSpan="6" className="px-6 py-4 text-center">
                     Loading...
                   </td>
                 </tr>
               ) : loans.length === 0 ? (
                 <tr>
                   <td colSpan="6" className="px-6 py-4 text-center text-gray-500">
                     No loans found
                   </td>
                 </tr>
               ) : (
                 loans.map((loan) => (
                   <tr key={loan.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                       {loan.coopId}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(loan.loanAmount)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {dayjs(loan.loanDate).format("MMM D, YYYY")}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {dayjs(loan.repaymentDate).format("MMM D, YYYY")}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4">
                       <span
                         className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                           loan.status === 1
                             ? "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                             : "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                         }`}
                       >
                         {loan.status === 1 ? "Active" : "Completed"}
                       </span>
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                       <div className="flex items-center justify-end gap-2">
                         <Link to={`/loans/${loan.id}`}>
                           <Button size="sm" variant="outlined" className="gap-1">
                             <EyeIcon className="size-4" />
                             View
                           </Button>
                         </Link>
                         {loan.status === 1 && (
                           <Button
                             size="sm"
                             color="success"
                             className="gap-1"
                             onClick={() => handleCompleteLoan(loan.id)}
                           >
                             <CheckCircleIcon className="size-4" />
                             Complete
                           </Button>
                         )}
                       </div>
                     </td>
                   </tr>
                 ))
               )}
             </tbody>
           </table>
         </div>
       </Card>
     </div>
   </Page>
 );
}