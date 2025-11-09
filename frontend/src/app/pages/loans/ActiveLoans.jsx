// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { ArrowLeftIcon, CheckCircleIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";
import dayjs from "dayjs";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card } from "components/ui";
import { loansAPI } from "services/api";

// ----------------------------------------------------------------------

export default function ActiveLoans() {
 const [loans, setLoans] = useState([]);
 const [loading, setLoading] = useState(true);
 const [currentPage, setCurrentPage] = useState(1);
 const [pagination, setPagination] = useState({});

 const fetchActiveLoans = useCallback(async () => {
   try {
     setLoading(true);
     const response = await loansAPI.getActive({
       page: currentPage,
       per_page: 25,
     });
     
     // Handle Laravel API response format
     const data = response.data?.data?.data || response.data?.data || [];
     const meta = response.data?.data?.pagination || response.data?.meta || {};
  
     setLoans(data);
     setPagination(meta);
   } catch (error) {
     console.error("Error fetching active loans:", error);
     toast.error("Failed to load active loans");
   } finally {
     setLoading(false);
   }
 }, [currentPage]);

 useEffect(() => {
   fetchActiveLoans();
 }, [fetchActiveLoans]);

 const handleCompleteLoan = async (id) => {
   if (!window.confirm("Mark this loan as completed?")) {
     return;
   }

   try {
     await loansAPI.complete(id);
     toast.success("Loan marked as completed");
     fetchActiveLoans();
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
   <Page title="Active Loans">
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div className="flex items-center gap-3">
           <Link to="/loans">
             <Button variant="outlined" size="sm">
               <ArrowLeftIcon className="size-4" />
             </Button>
           </Link>
           <div>
             <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
               Active Loans
             </h2>
             <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
               Currently active member loans
             </p>
           </div>
         </div>
       </div>

       {/* Active Loans Table */}
       <Card className="overflow-hidden">
         <div className="overflow-x-auto">
           <table className="w-full">
             <thead className="bg-gray-50 dark:bg-dark-600">
               <tr>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Coop ID
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Member
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Amount
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Paid
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Balance
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Date
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Repayment Date
                 </th>
                 <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Actions
                 </th>
               </tr>
             </thead>
             <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
               {loading ? (
                 <tr>
                   <td colSpan="8" className="px-6 py-4 text-center">
                     Loading...
                   </td>
                 </tr>
               ) : loans.length === 0 ? (
                 <tr>
                   <td colSpan="8" className="px-6 py-4 text-center text-gray-500">
                     No active loans found
                   </td>
                 </tr>
               ) : (
                 loans.map((loan) => (
                   <tr key={loan.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                       {loan.coopId}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {loan.member?.surname} {loan.member?.otherNames}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(loan.loanAmount)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-green-600 dark:text-green-400">
                       {formatCurrency(loan.loanPaid || 0)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-semibold text-red-600 dark:text-red-400">
                       {formatCurrency(loan.loanBalance || loan.loanAmount)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {dayjs(loan.loanDate).format("MMM D, YYYY")}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {dayjs(loan.repaymentDate).format("MMM D, YYYY")}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                       <Button
                         size="sm"
                         color="success"
                         className="gap-1"
                         onClick={() => handleCompleteLoan(loan.id)}
                       >
                         <CheckCircleIcon className="size-4" />
                         Complete
                       </Button>
                     </td>
                   </tr>
                 ))
               )}
             </tbody>
           </table>
         </div>

         {/* Pagination */}
         {pagination.total > 0 && (
           <div className="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3 dark:border-dark-500 dark:bg-dark-700">
             <div className="text-sm text-gray-500 dark:text-dark-300">
               Showing {((currentPage - 1) * pagination.per_page) + 1} to{" "}
               {Math.min(currentPage * pagination.per_page, pagination.total)} of{" "}
               {pagination.total} results
             </div>
             <div className="flex gap-2">
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={currentPage === 1}
                 onClick={() => setCurrentPage(currentPage - 1)}
               >
                 Previous
               </Button>
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={currentPage >= pagination.total_pages}
                 onClick={() => setCurrentPage(currentPage + 1)}
               >
                 Next
               </Button>
             </div>
           </div>
         )}
       </Card>
     </div>
   </Page>
 );
}