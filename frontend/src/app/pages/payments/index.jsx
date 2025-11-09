// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";
import dayjs from "dayjs";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { paymentsAPI } from "services/api";

// ----------------------------------------------------------------------

export default function PaymentsList() {
 const [payments, setPayments] = useState([]);
 const [loading, setLoading] = useState(true);
 const [search, setSearch] = useState("");

 const fetchPayments = useCallback(async () => {
   try {
     setLoading(true);
     const response = await paymentsAPI.getAll({
       coop_id: search,
       per_page: 25,
     });
     
     const data = response.data?.data || [];
     setPayments(data);
   } catch (error) {
     console.error("Error fetching payments:", error);
     toast.error("Failed to load payments");
   } finally {
     setLoading(false);
   }
 }, [search]);

 useEffect(() => {
   fetchPayments();
 }, [fetchPayments]);

 const formatCurrency = (amount) => {
   return new Intl.NumberFormat("en-NG", {
     style: "currency",
     currency: "NGN",
   }).format(amount);
 };

 return (
   <Page title="Payments">
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div>
           <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
             Payments Management
           </h2>
           <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
             Track member payments
           </p>
         </div>
         <Link to="/payments/create">
           <Button color="primary" className="gap-2">
             <PlusIcon className="size-5" />
             Record Payment
           </Button>
         </Link>
       </div>

       {/* Search */}
       <Card className="p-5">
         <Input
           placeholder="Search by Coop ID..."
           value={search}
           onChange={(e) => setSearch(e.target.value)}
           prefix={
             <MagnifyingGlassIcon className="size-5 text-gray-400" />
           }
         />
       </Card>

       {/* Payments Table */}
       <Card className="overflow-hidden">
         <div className="overflow-x-auto">
           <table className="w-full">
             <thead className="bg-gray-50 dark:bg-dark-600">
               <tr>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Coop ID
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Payment Date
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Loan Amount
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Savings
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Shares
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Total Amount
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
               ) : payments.length === 0 ? (
                 <tr>
                   <td colSpan="6" className="px-6 py-4 text-center text-gray-500">
                     No payments found
                   </td>
                 </tr>
               ) : (
                 payments.map((payment) => (
                   <tr key={payment.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                       {payment.coopId}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {dayjs(payment.paymentDate).format("MMM D, YYYY")}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(payment.loanAmount || 0)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(payment.savingAmount || 0)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(payment.shareAmount || 0)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900 dark:text-dark-100">
                       {formatCurrency(payment.totalAmount)}
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